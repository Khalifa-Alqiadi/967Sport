<?php

namespace App\Http\Controllers\Custom;

use App\Http\Controllers\Controller;
use App\Models\Fixture;
use App\Models\League;
use App\Models\Season;
use App\Models\LeagueStanding;
use App\Models\Topic;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FootballController extends Controller
{
    public function localizedCompetitions(string $lang)
    {
        app()->setLocale($lang);

        return $this->competitions();
    }

    public function localizedLeague(Request $request, string $lang, int $league)
    {
        app()->setLocale($lang);

        return $this->league($request, $league);
    }

    public function localizedMatches(Request $request, string $lang)
    {
        app()->setLocale($lang);

        return $this->matches($request);
    }

    public function localizedMatch(string $lang, int $fixture)
    {
        app()->setLocale($lang);

        return $this->match($fixture);
    }

    public function competitions()
    {
        $leagues = League::query()
            ->where('status', 1)
            ->with(['country', 'seasons' => fn ($query) => $query->orderByDesc('is_current')->orderByDesc('starting_at')])
            ->withCount(['matches', 'seasons'])
            ->orderByDesc('is_home')
            ->orderBy('row_no')
            ->orderBy('name_ar')
            ->get();

        return view('frontEnd.matches.competitions', [
            'PageTitle' => __('matches.competitions'),
            'page_type' => 'football',
            'leagues' => $leagues,
        ]);
    }

    public function league(Request $request, int $league)
    {
        $league = League::query()->where('status', 1)->findOrFail($league);
        $seasons = Season::query()
            ->where('league_id', $league->id)
            ->orderByDesc('is_current')
            ->orderByDesc('starting_at')
            ->get();

        $season = $this->selectedSeason($request, $league, $seasons);
        $tab = in_array($request->query('tab'), ['overview', 'matches', 'news', 'statistics', 'standings'], true)
            ? $request->query('tab')
            : 'overview';

        $fixtureQuery = Fixture::query()
            ->where('league_id', $league->id)
            ->when($season, fn ($query) => $query->where('season_id', $season->id))
            ->with(['homeTeam', 'awayTeam', 'round']);

        $upcomingMatches = (clone $fixtureQuery)
            ->where('starting_at', '>=', now()->startOfDay())
            ->orderBy('starting_at')
            ->limit($tab === 'matches' ? 50 : 5)
            ->get();

        $completedMatches = (clone $fixtureQuery)
            ->where(fn ($query) => $query->where('is_finished', 1)->orWhere('starting_at', '<', now()->startOfDay()))
            ->orderByDesc('starting_at')
            ->limit($tab === 'matches' ? 50 : 5)
            ->get();

        $standings = LeagueStanding::query()
            ->where('league_id', $league->id)
            ->when($season, fn ($query) => $query->where('season_id', $season->id))
            ->with('participant')
            ->orderByRaw('CASE WHEN group_name IS NULL THEN 0 ELSE 1 END')
            ->orderBy('group_name')
            ->orderBy('position')
            ->get();

        $news = Topic::query()
            ->where('league_id', $league->id)
            ->where('status', 1)
            ->when($season, fn ($query) => $query->where(fn ($seasonQuery) => $seasonQuery
                ->where('season_id', $season->id)
                ->orWhereNull('season_id')))
            ->latest('date')
            ->latest('id')
            ->limit($tab === 'news' ? 24 : 4)
            ->get();

        $allFixtures = (clone $fixtureQuery)->get();
        $statistics = [
            'matches' => $allFixtures->count(),
            'finished' => $allFixtures->where('is_finished', true)->count(),
            'goals' => $allFixtures->sum(fn ($fixture) => (int) ($fixture->home_score ?? $fixture->ft_home_score ?? 0)
                + (int) ($fixture->away_score ?? $fixture->ft_away_score ?? 0)),
            'teams' => $allFixtures->pluck('home_team_id')->merge($allFixtures->pluck('away_team_id'))->filter()->unique()->count(),
        ];

        return view('frontEnd.matches.league', compact(
            'league',
            'seasons',
            'season',
            'tab',
            'upcomingMatches',
            'completedMatches',
            'standings',
            'news',
            'statistics'
        ) + [
            'PageTitle' => $this->localizedName($league),
            'page_type' => 'football',
        ]);
    }

    public function match(int $fixture)
    {
        $fixture = Fixture::query()
            ->with([
                'league',
                'season',
                'homeTeam',
                'awayTeam',
                'round',
                'stage',
                'events.team',
                'events.player',
                'events.assistPlayer',
            ])
            ->findOrFail($fixture);

        $relatedMatches = Fixture::query()
            ->where('league_id', $fixture->league_id)
            ->where('id', '<>', $fixture->id)
            ->with(['homeTeam', 'awayTeam', 'round'])
            ->orderByRaw('ABS(TIMESTAMPDIFF(SECOND, starting_at, ?))', [$fixture->starting_at])
            ->limit(4)
            ->get();

        $news = Topic::query()
            ->where('status', 1)
            ->where(fn ($query) => $query->where('fixture_id', $fixture->id)
                ->orWhere(fn ($leagueQuery) => $leagueQuery->whereNull('fixture_id')->where('league_id', $fixture->league_id)))
            ->latest('date')
            ->latest('id')
            ->limit(4)
            ->get();

        return view('frontEnd.matches.match', [
            'PageTitle' => $this->localizedName($fixture->homeTeam).' - '.$this->localizedName($fixture->awayTeam),
            'page_type' => 'football',
            'fixture' => $fixture,
            'relatedMatches' => $relatedMatches,
            'news' => $news,
        ]);
    }

    public function matches(Request $request)
    {
        $day = in_array($request->query('day'), ['yesterday', 'today', 'tomorrow', 'date'], true)
            ? $request->query('day')
            : 'today';

        $date = match ($day) {
            'yesterday' => now()->subDay(),
            'tomorrow' => now()->addDay(),
            'date' => $this->validatedDate($request->query('date')),
            default => now(),
        };

        $fixtures = Fixture::query()
            ->whereDate('starting_at', $date->toDateString())
            ->with(['league', 'season', 'homeTeam', 'awayTeam', 'round'])
            ->orderBy('starting_at')
            ->get();

        return view('frontEnd.matches.index', [
            'PageTitle' => __('matches.matches'),
            'page_type' => 'football',
            'day' => $day,
            'selectedDate' => $date,
            'fixturesByLeague' => $fixtures->groupBy('league_id'),
            'fixturesCount' => $fixtures->count(),
        ]);
    }

    private function selectedSeason(Request $request, League $league, $seasons): ?Season
    {
        $requestedId = (int) $request->query('season');
        if ($requestedId > 0) {
            $requested = $seasons->firstWhere('id', $requestedId);
            if ($requested) {
                return $requested;
            }
        }

        return $seasons->firstWhere('id', $league->current_season_id)
            ?: $seasons->firstWhere('is_current', true)
            ?: $seasons->first();
    }

    private function validatedDate(?string $date): Carbon
    {
        if ($date && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            try {
                return Carbon::createFromFormat('Y-m-d', $date)->startOfDay();
            } catch (\Throwable $exception) {
                // Fall through to today's date.
            }
        }

        return now()->startOfDay();
    }

    private function localizedName($model): string
    {
        if (!$model) {
            return __('matches.unknown');
        }

        $primary = app()->getLocale() === 'en' ? 'name_en' : 'name_ar';
        $fallback = $primary === 'name_en' ? 'name_ar' : 'name_en';

        return (string) ($model->{$primary} ?: $model->{$fallback} ?: __('matches.unknown'));
    }
}
