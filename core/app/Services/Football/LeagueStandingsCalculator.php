<?php

namespace App\Services\Football;

use App\Models\Fixture;
use App\Models\Group;
use App\Models\LeagueStanding;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LeagueStandingsCalculator
{
    private const FINISHED_STATES = ['FT', 'AET', 'PEN', 'AWARDED'];

    /**
     * The current Yemen FA table is ordered by points then goal difference.
     * FIFA's league-system fallback then uses goals scored and head-to-head results.
     */
    private const PRIMARY_SORT_FIELDS = ['points', 'goal_difference', 'goals_for'];

    public function recalculate(?int $leagueId = null, ?int $seasonId = null): array
    {
        $scopes = Fixture::query()
            ->select(['league_id', 'season_id'])
            ->whereNotNull('league_id')
            ->whereNotNull('season_id')
            ->when($leagueId, fn ($query) => $query->where('league_id', $leagueId))
            ->when($seasonId, fn ($query) => $query->where('season_id', $seasonId))
            ->distinct()
            ->get();

        $summary = ['competitions' => 0, 'tables' => 0, 'teams' => 0, 'matches' => 0];

        foreach ($scopes as $scope) {
            $result = $this->recalculateCompetition((int) $scope->league_id, (int) $scope->season_id);
            $summary['competitions']++;
            $summary['tables'] += $result['tables'];
            $summary['teams'] += $result['teams'];
            $summary['matches'] += $result['matches'];
        }

        return $summary;
    }

    public function recalculateCompetition(int $leagueId, int $seasonId): array
    {
        $fixtures = Fixture::query()
            ->where('league_id', $leagueId)
            ->where('season_id', $seasonId)
            ->whereNotNull('home_team_id')
            ->whereNotNull('away_team_id')
            ->orderBy('starting_at')
            ->orderBy('id')
            ->get();

        $groupNames = Group::query()
            ->whereIn('id', $fixtures->pluck('group_id')->filter()->unique())
            ->get()
            ->keyBy('id');

        $tables = $fixtures->groupBy(fn (Fixture $fixture) => $this->scopeKey($fixture->group_id));
        $summary = ['tables' => 0, 'teams' => 0, 'matches' => 0];

        DB::transaction(function () use ($leagueId, $seasonId, $tables, $groupNames, &$summary): void {
            foreach ($tables as $scopeKey => $scopeFixtures) {
                $groupId = $scopeFixtures->first()?->group_id;
                $group = $groupId ? $groupNames->get($groupId) : null;
                $groupName = $group?->name_ar ?: $group?->name_en;
                $result = $this->calculateTable($scopeFixtures);

                $existing = LeagueStanding::query()
                    ->where('league_id', $leagueId)
                    ->where('season_id', $seasonId)
                    ->where('scope_key', $scopeKey)
                    ->get()
                    ->keyBy('team_id');

                $rows = $result['rows'];
                foreach ($rows as &$row) {
                    $manualAdjustment = (int) ($existing->get($row['team_id'])?->manual_points_adjustment ?? 0);
                    $row['manual_points_adjustment'] = $manualAdjustment;
                    $row['points'] = $row['base_points'] + $row['fixture_points_adjustment'] + $manualAdjustment;
                }
                unset($row);
                $this->sortRows($rows, $scopeFixtures);

                foreach ($rows as $position => $row) {
                    $row['position'] = $position + 1;
                    $row['league_id'] = $leagueId;
                    $row['season_id'] = $seasonId;
                    $row['group_id'] = $groupId;
                    $row['scope_key'] = $scopeKey;
                    $row['group_name'] = $groupName;
                    $row['calculated_at'] = now();

                    LeagueStanding::query()->updateOrCreate([
                        'league_id' => $leagueId,
                        'season_id' => $seasonId,
                        'scope_key' => $scopeKey,
                        'team_id' => $row['team_id'],
                    ], $row);
                }

                LeagueStanding::query()
                    ->where('league_id', $leagueId)
                    ->where('season_id', $seasonId)
                    ->where('scope_key', $scopeKey)
                    ->when($result['team_ids'], fn ($query) => $query->whereNotIn('team_id', $result['team_ids']))
                    ->delete();

                $summary['tables']++;
                $summary['teams'] += count($result['team_ids']);
                $summary['matches'] += $result['matches'];
            }
        });

        return $summary;
    }

    private function calculateTable(Collection $fixtures): array
    {
        $rows = [];
        $countedMatches = 0;

        foreach ($fixtures as $fixture) {
            $homeId = (int) $fixture->home_team_id;
            $awayId = (int) $fixture->away_team_id;
            $rows[$homeId] ??= $this->emptyRow($homeId);
            $rows[$awayId] ??= $this->emptyRow($awayId);

            $score = $this->standingsScore($fixture);
            if (!$this->isCounted($fixture) || $score === null) {
                continue;
            }

            [$homeScore, $awayScore] = $score;
            $countedMatches++;

            $rows[$homeId]['played']++;
            $rows[$awayId]['played']++;
            $rows[$homeId]['goals_for'] += $homeScore;
            $rows[$homeId]['goals_against'] += $awayScore;
            $rows[$awayId]['goals_for'] += $awayScore;
            $rows[$awayId]['goals_against'] += $homeScore;
            $rows[$homeId]['fixture_points_adjustment'] += (int) $fixture->standing_home_points_adjustment;
            $rows[$awayId]['fixture_points_adjustment'] += (int) $fixture->standing_away_points_adjustment;

            if ($homeScore > $awayScore) {
                $this->recordResult($rows[$homeId], 'won', 'W', 3);
                $this->recordResult($rows[$awayId], 'lost', 'L', 0);
            } elseif ($homeScore < $awayScore) {
                $this->recordResult($rows[$homeId], 'lost', 'L', 0);
                $this->recordResult($rows[$awayId], 'won', 'W', 3);
            } else {
                $this->recordResult($rows[$homeId], 'draw', 'D', 1);
                $this->recordResult($rows[$awayId], 'draw', 'D', 1);
            }
        }

        foreach ($rows as &$row) {
            $row['goal_difference'] = $row['goals_for'] - $row['goals_against'];
            $row['form'] = implode('', array_slice($row['form_results'], -5));
            $row['recent_form_points'] = array_sum(array_slice($row['form_points'], -5));
            $row['points'] = $row['base_points'] + $row['fixture_points_adjustment'];
            unset($row['form_results'], $row['form_points']);
        }
        unset($row);

        $this->sortRows($rows, $fixtures);

        return [
            'rows' => array_values($rows),
            'team_ids' => array_column($rows, 'team_id'),
            'matches' => $countedMatches,
        ];
    }

    private function sortRows(array &$rows, Collection $fixtures): void
    {
        usort($rows, function (array $left, array $right): int {
            foreach (self::PRIMARY_SORT_FIELDS as $field) {
                if ($left[$field] !== $right[$field]) {
                    return $right[$field] <=> $left[$field];
                }
            }

            return $left['team_id'] <=> $right['team_id'];
        });

        $offset = 0;
        while ($offset < count($rows)) {
            $length = 1;
            while (isset($rows[$offset + $length])
                && $this->hasSamePrimaryRank($rows[$offset], $rows[$offset + $length])) {
                $length++;
            }

            if ($length > 1) {
                $tiedRows = array_slice($rows, $offset, $length);
                $headToHead = $this->headToHeadStats(array_column($tiedRows, 'team_id'), $fixtures);

                usort($tiedRows, function (array $left, array $right) use ($headToHead): int {
                    foreach (['points', 'goal_difference', 'goals_for'] as $field) {
                        $leftValue = $headToHead[$left['team_id']][$field] ?? 0;
                        $rightValue = $headToHead[$right['team_id']][$field] ?? 0;
                        if ($leftValue !== $rightValue) {
                            return $rightValue <=> $leftValue;
                        }
                    }

                    if ($left['won'] !== $right['won']) {
                        return $right['won'] <=> $left['won'];
                    }

                    return $left['team_id'] <=> $right['team_id'];
                });

                array_splice($rows, $offset, $length, $tiedRows);
            }

            $offset += $length;
        }
    }

    private function hasSamePrimaryRank(array $left, array $right): bool
    {
        foreach (self::PRIMARY_SORT_FIELDS as $field) {
            if ($left[$field] !== $right[$field]) {
                return false;
            }
        }

        return true;
    }

    private function headToHeadStats(array $teamIds, Collection $fixtures): array
    {
        $teamLookup = array_fill_keys($teamIds, true);
        $stats = [];
        foreach ($teamIds as $teamId) {
            $stats[$teamId] = ['points' => 0, 'goals_for' => 0, 'goals_against' => 0, 'goal_difference' => 0];
        }

        foreach ($fixtures as $fixture) {
            $homeId = (int) $fixture->home_team_id;
            $awayId = (int) $fixture->away_team_id;
            if (!isset($teamLookup[$homeId], $teamLookup[$awayId]) || !$this->isCounted($fixture)) {
                continue;
            }

            $score = $this->standingsScore($fixture);
            if ($score === null) {
                continue;
            }

            [$homeScore, $awayScore] = $score;
            $stats[$homeId]['goals_for'] += $homeScore;
            $stats[$homeId]['goals_against'] += $awayScore;
            $stats[$awayId]['goals_for'] += $awayScore;
            $stats[$awayId]['goals_against'] += $homeScore;

            if ($homeScore > $awayScore) {
                $stats[$homeId]['points'] += 3;
            } elseif ($homeScore < $awayScore) {
                $stats[$awayId]['points'] += 3;
            } else {
                $stats[$homeId]['points']++;
                $stats[$awayId]['points']++;
            }
        }

        foreach ($stats as &$row) {
            $row['goal_difference'] = $row['goals_for'] - $row['goals_against'];
        }
        unset($row);

        return $stats;
    }

    private function emptyRow(int $teamId): array
    {
        return [
            'team_id' => $teamId,
            'played' => 0,
            'won' => 0,
            'draw' => 0,
            'lost' => 0,
            'goals_for' => 0,
            'goals_against' => 0,
            'goal_difference' => 0,
            'base_points' => 0,
            'fixture_points_adjustment' => 0,
            'points' => 0,
            'recent_form_points' => 0,
            'form' => null,
            'form_results' => [],
            'form_points' => [],
        ];
    }

    private function recordResult(array &$row, string $result, string $form, int $points): void
    {
        $row[$result]++;
        $row['base_points'] += $points;
        $row['form_results'][] = $form;
        $row['form_points'][] = $points;
    }

    private function isCounted(Fixture $fixture): bool
    {
        return (bool) $fixture->counts_for_standings
            && ((bool) $fixture->is_finished || in_array($fixture->state_code, self::FINISHED_STATES, true));
    }

    private function standingsScore(Fixture $fixture): ?array
    {
        if ($fixture->standing_home_score !== null && $fixture->standing_away_score !== null) {
            return [(int) $fixture->standing_home_score, (int) $fixture->standing_away_score];
        }

        if ($fixture->ft_home_score !== null && $fixture->ft_away_score !== null) {
            return [(int) $fixture->ft_home_score, (int) $fixture->ft_away_score];
        }

        if ($fixture->home_score !== null && $fixture->away_score !== null) {
            return [(int) $fixture->home_score, (int) $fixture->away_score];
        }

        return null;
    }

    private function scopeKey(?int $groupId): string
    {
        return $groupId ? 'group:'.$groupId : 'overall';
    }
}
