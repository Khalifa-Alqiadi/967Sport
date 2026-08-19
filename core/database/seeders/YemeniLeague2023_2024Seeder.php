<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class YemeniLeague2023_2024Seeder extends Seeder
{
    private const LEAGUE_ID = 1;
    private const SEASON_ID = 9672301;
    private const GROUP_STAGE_ID = 96723011;
    private const PLAYOFF_STAGE_ID = 96723012;
    private const GROUP_ONE_ID = 967230111;
    private const GROUP_TWO_ID = 967230112;
    private const SOURCE_URL = 'https://www.rsssf.org/tablesy/yemen2024.html';

    public function run(): void
    {
        DB::transaction(function (): void {
            $countryId = DB::table('countries')->where('code', 'YE')->value('id');

            if (!$countryId) {
                throw new RuntimeException('Yemen (YE) must exist in the countries table before running this seeder.');
            }

            $now = now();

            DB::table('leagues')->updateOrInsert(
                ['id' => self::LEAGUE_ID],
                [
                    'sport_id' => 1,
                    'country_id' => $countryId,
                    'status' => true,
                    'is_home' => true,
                    'major_competitions' => true,
                    'row_no' => 1,
                    'name_ar' => 'الدوري اليمني للدرجة الأولى',
                    'name_en' => 'Yemeni League Division One',
                    'short_code' => 'YEM',
                    'current_season_id' => self::SEASON_ID,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );

            DB::table('seasons')->updateOrInsert(
                ['id' => self::SEASON_ID],
                [
                    'league_id' => self::LEAGUE_ID,
                    'name' => '2023-2024',
                    'starting_at' => '2023-10-01 00:00:00',
                    'ending_at' => '2024-02-11 23:59:59',
                    'is_current' => false,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );

            $teams = $this->teams();
            $teamIds = [];

            foreach ($teams as $team) {
                $teamIds[$team['key']] = $team['id'];

                DB::table('teams')->updateOrInsert(
                    ['id' => $team['id']],
                    [
                        'country_id' => $countryId,
                        'sport_id' => 1,
                        'name_ar' => $team['name_ar'],
                        'name_en' => $team['name_en'],
                        'short_code' => $team['short_code'],
                        'status' => true,
                        'row_no' => $team['row_no'],
                        'type' => 'domestic',
                        'placeholder' => false,
                        'major_competitions' => true,
                        'major_national_teams' => false,
                        'is_home' => true,
                        'updated_at' => $now,
                        'created_at' => $now,
                    ]
                );

                DB::table('active_seasons')->updateOrInsert(
                    [
                        'team_id' => $team['id'],
                        'season_id' => self::SEASON_ID,
                        'league_id' => self::LEAGUE_ID,
                    ],
                    ['updated_at' => $now, 'created_at' => $now]
                );
            }

            $this->seedStagesAndGroups($now);
            $roundIds = $this->seedRounds($now);
            $this->seedFixtures($teamIds, $roundIds, $now);
            $this->seedStandings($teamIds, $roundIds[12], $now);
            $this->syncRoundDates($roundIds);
        });
    }

    private function teams(): array
    {
        return [
            ['id' => 967101, 'key' => 'ahli', 'name_ar' => 'أهلي صنعاء', 'name_en' => "Al-Ahli Sana'a", 'short_code' => 'AHL', 'row_no' => 1],
            ['id' => 967102, 'key' => 'tadamun', 'name_ar' => 'تضامن حضرموت', 'name_en' => 'Al-Tadamun Hadramaut', 'short_code' => 'TDH', 'row_no' => 2],
            ['id' => 967103, 'key' => 'urooba', 'name_ar' => 'العروبة زبيد', 'name_en' => 'Al-Urooba Zabid', 'short_code' => 'URB', 'row_no' => 3],
            ['id' => 967104, 'key' => 'ittihad_ibb', 'name_ar' => 'اتحاد إب', 'name_en' => 'Al-Ittihad Ibb', 'short_code' => 'ITB', 'row_no' => 4],
            ['id' => 967105, 'key' => 'hilal', 'name_ar' => 'الهلال الساحلي', 'name_en' => 'Al-Hilal Al-Sahely', 'short_code' => 'HIL', 'row_no' => 5],
            ['id' => 967106, 'key' => 'samoon', 'name_ar' => 'سمعون الشحر', 'name_en' => 'Samoon Al-Shihr', 'short_code' => 'SAM', 'row_no' => 6],
            ['id' => 967107, 'key' => 'saqr', 'name_ar' => 'الصقر تعز', 'name_en' => "Al-Saqr Ta'izz", 'short_code' => 'SQR', 'row_no' => 7],
            ['id' => 967108, 'key' => 'wahda', 'name_ar' => 'الوحدة صنعاء', 'name_en' => "Al-Wahda Sana'a", 'short_code' => 'WHD', 'row_no' => 8],
            ['id' => 967109, 'key' => 'shaab_h', 'name_ar' => 'شعب حضرموت', 'name_en' => 'Al-Shaab Hadramaut', 'short_code' => 'SHH', 'row_no' => 9],
            ['id' => 967110, 'key' => 'fahman', 'name_ar' => 'فحمان أبين', 'name_en' => 'Fahman Abyan', 'short_code' => 'FAH', 'row_no' => 10],
            ['id' => 967111, 'key' => 'salam', 'name_ar' => 'سلام الغرفة', 'name_en' => 'Salam Al-Garfa', 'short_code' => 'SLG', 'row_no' => 11],
            ['id' => 967112, 'key' => 'yarmuk', 'name_ar' => 'اليرموك الروضة', 'name_en' => 'Al-Yarmouk Al-Rawda', 'short_code' => 'YAR', 'row_no' => 12],
            ['id' => 967113, 'key' => 'shaab_ibb', 'name_ar' => 'شعب إب', 'name_en' => 'Al-Shaab Ibb', 'short_code' => 'SHI', 'row_no' => 13],
            ['id' => 967114, 'key' => 'taliya', 'name_ar' => 'الطليعة تعز', 'name_en' => "Al-Tali'aa Ta'izz", 'short_code' => 'TAL', 'row_no' => 14],
        ];
    }

    private function seedStagesAndGroups($now): void
    {
        DB::table('stages')->updateOrInsert(
            ['id' => self::GROUP_STAGE_ID],
            [
                'league_id' => self::LEAGUE_ID,
                'season_id' => self::SEASON_ID,
                'type_id' => 1,
                'name_ar' => 'دور المجموعات',
                'name_en' => 'Group Stage',
                'type_name' => 'Group Stage',
                'sort_order' => 1,
                'finished' => true,
                'is_current' => false,
                'starting_at' => '2023-10-01 00:00:00',
                'ending_at' => '2024-01-01 23:59:59',
                'payload' => json_encode(['source' => self::SOURCE_URL], JSON_UNESCAPED_SLASHES),
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        DB::table('stages')->updateOrInsert(
            ['id' => self::PLAYOFF_STAGE_ID],
            [
                'league_id' => self::LEAGUE_ID,
                'season_id' => self::SEASON_ID,
                'type_id' => 2,
                'name_ar' => 'المربع الذهبي',
                'name_en' => 'Championship Playoff',
                'type_name' => 'Playoff',
                'sort_order' => 2,
                'finished' => true,
                'is_current' => false,
                'starting_at' => '2024-01-27 00:00:00',
                'ending_at' => '2024-02-11 23:59:59',
                'payload' => json_encode(['source' => self::SOURCE_URL], JSON_UNESCAPED_SLASHES),
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        foreach ([
            [self::GROUP_ONE_ID, 'المجموعة الأولى', 'Group 1', 1],
            [self::GROUP_TWO_ID, 'المجموعة الثانية', 'Group 2', 2],
        ] as [$id, $nameAr, $nameEn, $order]) {
            DB::table('groups')->updateOrInsert(
                ['id' => $id],
                [
                    'league_id' => self::LEAGUE_ID,
                    'season_id' => self::SEASON_ID,
                    'stage_id' => self::GROUP_STAGE_ID,
                    'name_ar' => $nameAr,
                    'name_en' => $nameEn,
                    'sort_order' => $order,
                    'finished' => true,
                    'is_current' => false,
                    'starting_at' => '2023-10-01 00:00:00',
                    'ending_at' => '2024-01-01 23:59:59',
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }

    private function seedRounds($now): array
    {
        $ids = [];

        for ($number = 1; $number <= 12; $number++) {
            $ids[$number] = 96723100 + $number;
            DB::table('rounds')->updateOrInsert(
                ['id' => $ids[$number]],
                [
                    'league_id' => self::LEAGUE_ID,
                    'season_id' => self::SEASON_ID,
                    'stage_id' => self::GROUP_STAGE_ID,
                    'name' => 'الجولة '.$number,
                    'finished' => true,
                    'is_current' => false,
                    'games_in_current_week' => false,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        foreach ([
            'semi_first' => [96723201, 'نصف النهائي - الذهاب'],
            'semi_second' => [96723202, 'نصف النهائي - الإياب'],
            'third' => [96723203, 'مباراة المركز الثالث'],
            'final' => [96723204, 'النهائي'],
        ] as $key => [$id, $name]) {
            $ids[$key] = $id;
            DB::table('rounds')->updateOrInsert(
                ['id' => $id],
                [
                    'league_id' => self::LEAGUE_ID,
                    'season_id' => self::SEASON_ID,
                    'stage_id' => self::PLAYOFF_STAGE_ID,
                    'name' => $name,
                    'finished' => true,
                    'is_current' => false,
                    'games_in_current_week' => false,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        return $ids;
    }

    private function seedFixtures(array $teamIds, array $roundIds, $now): void
    {
        $fixtureId = 967240001;

        foreach ($this->fixtures() as $fixture) {
            [$date, $roundKey, $groupNumber, $home, $away, $homeScore, $awayScore] = $fixture;
            $penHome = $fixture[7] ?? null;
            $penAway = $fixture[8] ?? null;
            $note = $fixture[9] ?? null;
            $stageId = $groupNumber ? self::GROUP_STAGE_ID : self::PLAYOFF_STAGE_ID;
            $groupId = $groupNumber === 1 ? self::GROUP_ONE_ID : ($groupNumber === 2 ? self::GROUP_TWO_ID : null);
            $location = $this->fixtureLocation($groupNumber, $roundKey, $home);

            DB::table('fixtures')->updateOrInsert(
                ['id' => $fixtureId++],
                [
                    'league_id' => self::LEAGUE_ID,
                    'season_id' => self::SEASON_ID,
                    'round_id' => $roundIds[$roundKey],
                    'stage_id' => $stageId,
                    'group_id' => $groupId,
                    'home_team_id' => $teamIds[$home],
                    'away_team_id' => $teamIds[$away],
                    // The source publishes dates but not verified kick-off times.
                    'starting_at' => $date.' 00:00:00',
                    'state_name' => 'Finished',
                    'state_code' => 'FT',
                    'home_score' => $homeScore,
                    'away_score' => $awayScore,
                    'is_finished' => true,
                    'ft_home_score' => $homeScore,
                    'ft_away_score' => $awayScore,
                    'pen_home' => $penHome,
                    'pen_away' => $penAway,
                    'minute' => 90,
                    'payload' => json_encode(array_filter([
                        'source' => self::SOURCE_URL,
                        'kickoff_time_known' => false,
                        'location_ar' => $location,
                        'note' => $note,
                    ], static fn ($value) => $value !== null), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }

    private function fixtures(): array
    {
        return [
            // Group 1 — first phase in Sana'a.
            ['2023-10-06', 1, 1, 'samoon', 'urooba', 0, 2],
            ['2023-10-07', 1, 1, 'ittihad_ibb', 'ahli', 0, 1],
            ['2023-10-11', 2, 1, 'tadamun', 'samoon', 1, 2],
            ['2023-10-13', 2, 1, 'urooba', 'ittihad_ibb', 0, 1],
            ['2023-10-16', 3, 1, 'hilal', 'ahli', 0, 4],
            ['2023-10-17', 3, 1, 'tadamun', 'ittihad_ibb', 1, 0],
            ['2023-10-20', 4, 1, 'hilal', 'urooba', 1, 1],
            ['2023-10-21', 4, 1, 'samoon', 'ittihad_ibb', 1, 2],
            ['2023-10-24', 5, 1, 'ahli', 'urooba', 0, 0],
            ['2023-10-25', 5, 1, 'hilal', 'tadamun', 1, 1],
            ['2023-10-29', 6, 1, 'ahli', 'tadamun', 0, 1],
            ['2023-10-30', 6, 1, 'samoon', 'hilal', 0, 0],
            ['2023-11-02', 7, 1, 'urooba', 'tadamun', 0, 0],
            ['2023-11-03', 7, 1, 'ittihad_ibb', 'hilal', 0, 1],
            ['2023-11-04', 7, 1, 'ahli', 'samoon', 4, 0],
            ['2023-12-14', 8, 1, 'samoon', 'ahli', 0, 1],
            ['2023-12-15', 8, 1, 'hilal', 'ittihad_ibb', 0, 0],
            ['2023-12-16', 8, 1, 'tadamun', 'urooba', 3, 2],
            ['2023-12-18', 9, 1, 'hilal', 'samoon', 2, 1],
            ['2023-12-19', 9, 1, 'tadamun', 'ahli', 0, 2],
            ['2023-12-20', 9, 1, 'ittihad_ibb', 'urooba', 2, 2],
            ['2023-12-22', 10, 1, 'tadamun', 'hilal', 0, 0],
            ['2023-12-23', 10, 1, 'ittihad_ibb', 'samoon', 1, 0],
            ['2023-12-24', 10, 1, 'urooba', 'ahli', 1, 2],
            ['2023-12-27', 11, 1, 'samoon', 'tadamun', 1, 2],
            ['2023-12-28', 11, 1, 'urooba', 'hilal', 1, 0],
            ['2023-12-29', 11, 1, 'ahli', 'ittihad_ibb', 1, 0],
            ['2023-12-31', 12, 1, 'ittihad_ibb', 'tadamun', 2, 5],
            ['2023-12-31', 12, 1, 'urooba', 'samoon', 4, 1],
            ['2024-01-01', 12, 1, 'ahli', 'hilal', 6, 0],

            // Group 2 — includes the two officially awarded results.
            ['2023-10-02', 1, 2, 'yarmuk', 'salam', 0, 3, null, null, 'نتيجة اعتبارية'],
            ['2023-10-03', 1, 2, 'shaab_ibb', 'wahda', 3, 0, null, null, 'نتيجة اعتبارية'],
            ['2023-10-06', 2, 2, 'shaab_h', 'salam', 1, 1],
            ['2023-10-08', 2, 2, 'yarmuk', 'shaab_ibb', 0, 0],
            ['2023-10-11', 3, 2, 'wahda', 'fahman', 3, 1],
            ['2023-10-12', 3, 2, 'shaab_h', 'shaab_ibb', 5, 2],
            ['2023-10-15', 4, 2, 'fahman', 'yarmuk', 1, 2],
            ['2023-10-16', 4, 2, 'salam', 'shaab_ibb', 2, 1],
            ['2023-10-19', 5, 2, 'wahda', 'yarmuk', 2, 0],
            ['2023-10-20', 5, 2, 'fahman', 'shaab_h', 3, 0],
            ['2023-10-24', 6, 2, 'wahda', 'shaab_h', 1, 1],
            ['2023-10-25', 6, 2, 'salam', 'fahman', 1, 3],
            ['2023-10-28', 7, 2, 'yarmuk', 'shaab_h', 0, 4],
            ['2023-10-29', 7, 2, 'shaab_ibb', 'fahman', 1, 4],
            ['2023-10-30', 7, 2, 'wahda', 'salam', 1, 0],
            ['2023-12-14', 8, 2, 'salam', 'wahda', 1, 2],
            ['2023-12-15', 8, 2, 'fahman', 'shaab_ibb', 2, 2],
            ['2023-12-16', 8, 2, 'shaab_h', 'yarmuk', 1, 2],
            ['2023-12-18', 9, 2, 'fahman', 'salam', 0, 0],
            ['2023-12-19', 9, 2, 'shaab_h', 'wahda', 0, 0],
            ['2023-12-20', 9, 2, 'shaab_ibb', 'yarmuk', 0, 1],
            ['2023-12-22', 10, 2, 'shaab_h', 'fahman', 1, 0],
            ['2023-12-23', 10, 2, 'shaab_ibb', 'salam', 3, 2],
            ['2023-12-24', 10, 2, 'yarmuk', 'wahda', 1, 1],
            ['2023-12-27', 11, 2, 'yarmuk', 'fahman', 0, 2],
            ['2023-12-27', 11, 2, 'salam', 'shaab_h', 0, 1],
            ['2023-12-28', 11, 2, 'wahda', 'shaab_ibb', 1, 0],
            ['2023-12-30', 12, 2, 'salam', 'yarmuk', 3, 1],
            ['2024-01-01', 12, 2, 'shaab_ibb', 'shaab_h', 1, 2],
            ['2024-01-01', 12, 2, 'fahman', 'wahda', 1, 2],

            // Golden square.
            ['2024-01-27', 'semi_first', null, 'shaab_h', 'ahli', 0, 0],
            ['2024-01-28', 'semi_first', null, 'tadamun', 'wahda', 0, 0],
            ['2024-02-06', 'semi_second', null, 'ahli', 'shaab_h', 1, 0],
            ['2024-02-07', 'semi_second', null, 'wahda', 'tadamun', 1, 1, 2, 4, 'تأهل تضامن حضرموت بركلات الترجيح'],
            ['2024-02-10', 'third', null, 'wahda', 'shaab_h', 0, 0, 2, 4, 'فاز شعب حضرموت بركلات الترجيح'],
            ['2024-02-11', 'final', null, 'ahli', 'tadamun', 2, 0, null, null, 'توج أهلي صنعاء بطلًا للدوري'],
        ];
    }

    private function fixtureLocation(?int $groupNumber, int|string $roundKey, string $home): string
    {
        if ($groupNumber === 1) {
            if (is_int($roundKey) && $roundKey <= 7) {
                return 'صنعاء';
            }
            if (($roundKey === 8 && $home === 'samoon') || ($roundKey === 12 && $home === 'ittihad_ibb')) {
                return 'تريم';
            }
            return 'سيئون';
        }

        if ($groupNumber === 2) {
            return is_int($roundKey) && $roundKey <= 7 ? 'سيئون' : 'صنعاء';
        }

        return $roundKey === 'semi_first' ? 'استاد الفقيد بارادم - المكلا' : 'استاد الوحدة - صنعاء';
    }

    private function seedStandings(array $teamIds, int $roundId, $now): void
    {
        $tables = [
            [self::GROUP_ONE_ID, 'المجموعة الأولى', [
                ['ahli', 1, 25, 10, 8, 1, 1, 21, 2],
                ['tadamun', 2, 18, 10, 5, 3, 2, 14, 10],
                ['urooba', 3, 13, 10, 3, 4, 3, 13, 10],
                ['ittihad_ibb', 4, 11, 10, 3, 2, 5, 8, 12],
                ['hilal', 5, 11, 10, 2, 5, 3, 5, 14],
                ['samoon', 6, 4, 10, 1, 1, 8, 6, 19],
            ]],
            [self::GROUP_TWO_ID, 'المجموعة الثانية', [
                ['wahda', 1, 21, 10, 6, 3, 1, 13, 8],
                ['shaab_h', 2, 18, 10, 5, 3, 2, 16, 10],
                ['fahman', 3, 14, 10, 4, 2, 4, 17, 12],
                ['salam', 4, 11, 10, 3, 2, 5, 13, 13],
                ['yarmuk', 5, 11, 10, 3, 2, 5, 7, 17],
                ['shaab_ibb', 6, 8, 10, 2, 2, 6, 13, 19],
            ]],
        ];

        $standingId = 967250001;

        foreach ($tables as [$groupId, $groupName, $rows]) {
            foreach ($rows as [$team, $position, $points, $played, $won, $draw, $lost, $goalsFor, $goalsAgainst]) {
                DB::table('standings')->updateOrInsert(
                    ['id' => $standingId++],
                    [
                        'league_id' => self::LEAGUE_ID,
                        'season_id' => self::SEASON_ID,
                        'stage_id' => self::GROUP_STAGE_ID,
                        'round_id' => $roundId,
                        'group_id' => $groupId,
                        'team_id' => $teamIds[$team],
                        'participant_id' => $teamIds[$team],
                        'group_name' => $groupName,
                        'standing_type' => 'total',
                        'position' => $position,
                        'points' => $points,
                        'played' => $played,
                        'won' => $won,
                        'draw' => $draw,
                        'lost' => $lost,
                        'goals_for' => $goalsFor,
                        'goals_against' => $goalsAgainst,
                        'goal_difference' => $goalsFor - $goalsAgainst,
                        'payload_json' => json_encode(['source' => self::SOURCE_URL], JSON_UNESCAPED_SLASHES),
                        'synced_at' => $now,
                        'updated_at' => $now,
                        'created_at' => $now,
                    ]
                );
            }
        }
    }

    private function syncRoundDates(array $roundIds): void
    {
        foreach ($roundIds as $roundId) {
            $range = DB::table('fixtures')
                ->where('season_id', self::SEASON_ID)
                ->where('round_id', $roundId)
                ->selectRaw('MIN(starting_at) AS starts_at, MAX(starting_at) AS ends_at')
                ->first();

            DB::table('rounds')->where('id', $roundId)->update([
                'starting_at' => $range->starts_at,
                'ending_at' => $range->ends_at,
            ]);
        }
    }
}
