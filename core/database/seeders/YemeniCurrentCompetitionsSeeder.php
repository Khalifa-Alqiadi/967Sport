<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class YemeniCurrentCompetitionsSeeder extends Seeder
{
    private const DIVISION_ONE = 1;
    private const DIVISION_TWO = 96702;
    private const DIVISION_THREE = 96703;
    private const REPUBLIC_CUP = 96704;

    private const DIVISION_ONE_SEASON = 9672601;
    private const DIVISION_TWO_SEASON = 9672402;
    private const DIVISION_THREE_SEASON = 9672603;
    private const REPUBLIC_CUP_SEASON = 9672604;

    private const DIVISION_ONE_STAGE = 96726011;
    private const DIVISION_TWO_STAGE = 96724021;
    private const DIVISION_THREE_STAGE = 96726031;
    private const REPUBLIC_CUP_STAGE = 96726041;

    private const LEAGUE_SOURCE = 'https://jdwel.com/2025-2026-yemeni-league-fixtures/';
    private const FEDERATION_SOURCE = 'https://yemenfa.co/';

    public function run(): void
    {
        DB::transaction(function (): void {
            $countryId = DB::table('countries')->where('code', 'YE')->value('id');

            if (!$countryId) {
                throw new RuntimeException('Yemen (YE) must exist in the countries table before running this seeder.');
            }

            $now = now();
            $this->seedCompetitions($countryId, $now);
            $teamIds = $this->seedTeams($countryId, $now);
            $this->seedDivisionOne($teamIds, $now);
            $this->seedDivisionTwo($teamIds, $now);
            $this->seedCompetitionParticipants(self::REPUBLIC_CUP, self::REPUBLIC_CUP_SEASON, array_keys($this->teams()), $teamIds, $now);
            $this->seedCurrentCompetitionStages($now);
        });
    }

    private function seedCompetitions(int $countryId, $now): void
    {
        $competitions = [
            [self::DIVISION_ONE, self::DIVISION_ONE_SEASON, 'الدوري اليمني للدرجة الأولى', 'Yemeni League Division One', 'YEM1', 1, true],
            [self::DIVISION_TWO, self::DIVISION_TWO_SEASON, 'الدوري اليمني للدرجة الثانية', 'Yemeni League Division Two', 'YEM2', 2, false],
            [self::DIVISION_THREE, self::DIVISION_THREE_SEASON, 'تصفيات الدوري اليمني للدرجة الثالثة', 'Yemeni League Division Three Qualifiers', 'YEM3', 3, false],
            [self::REPUBLIC_CUP, self::REPUBLIC_CUP_SEASON, 'كأس الجمهورية اليمنية', 'Yemen Republic Cup', 'YCRC', 4, true],
        ];

        foreach ($competitions as [$leagueId, $seasonId, $nameAr, $nameEn, $code, $row, $major]) {
            DB::table('leagues')->updateOrInsert(
                ['id' => $leagueId],
                [
                    'sport_id' => 1,
                    'country_id' => $countryId,
                    'status' => true,
                    'is_home' => true,
                    'major_competitions' => $major,
                    'row_no' => $row,
                    'name_ar' => $nameAr,
                    'name_en' => $nameEn,
                    'short_code' => $code,
                    'current_season_id' => $seasonId,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        $seasons = [
            [self::DIVISION_ONE_SEASON, self::DIVISION_ONE, '2025-2026', '2026-04-30 00:00:00', '2027-03-31 23:59:59', true],
            // This is the federation's official season name, although it was played in 2025/2026 after postponements.
            [self::DIVISION_TWO_SEASON, self::DIVISION_TWO, '2023-2024', '2025-12-01 00:00:00', '2026-02-28 23:59:59', false],
            [self::DIVISION_THREE_SEASON, self::DIVISION_THREE, '2025-2026', '2026-06-15 00:00:00', '2026-12-31 23:59:59', true],
            [self::REPUBLIC_CUP_SEASON, self::REPUBLIC_CUP, '2025-2026', '2026-04-20 00:00:00', '2026-12-31 23:59:59', true],
        ];

        DB::table('seasons')->where('league_id', self::DIVISION_ONE)->update(['is_current' => false]);

        foreach ($seasons as [$id, $leagueId, $name, $startsAt, $endsAt, $current]) {
            DB::table('seasons')->updateOrInsert(
                ['id' => $id],
                [
                    'league_id' => $leagueId,
                    'name' => $name,
                    'starting_at' => $startsAt,
                    'ending_at' => $endsAt,
                    'is_current' => $current,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }

    private function seedTeams(int $countryId, $now): array
    {
        $ids = [];

        foreach ($this->teams() as $key => $team) {
            $ids[$key] = $team[0];
            DB::table('teams')->updateOrInsert(
                ['id' => $team[0]],
                [
                    'country_id' => $countryId,
                    'sport_id' => 1,
                    'name_ar' => $team[1],
                    'name_en' => $team[2],
                    'short_code' => $team[3],
                    'status' => true,
                    'row_no' => $team[0] - 967100,
                    'type' => 'domestic',
                    'placeholder' => false,
                    'major_competitions' => true,
                    'major_national_teams' => false,
                    'is_home' => true,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        return $ids;
    }

    private function teams(): array
    {
        return [
            'ahli_sanaa' => [967101, 'أهلي صنعاء', "Al-Ahli Sana'a", 'AHL'],
            'tadamun_h' => [967102, 'تضامن حضرموت', 'Al-Tadamun Hadramaut', 'TDH'],
            'urooba' => [967103, 'العروبة', 'Al-Urooba', 'URB'],
            'ittihad_ibb' => [967104, 'اتحاد إب', 'Al-Ittihad Ibb', 'ITB'],
            'hilal' => [967105, 'الهلال الساحلي', 'Al-Hilal Al-Sahely', 'HIL'],
            'samoon' => [967106, 'سمعون الشحر', 'Samoon Al-Shihr', 'SAM'],
            'saqr' => [967107, 'الصقر تعز', "Al-Saqr Ta'izz", 'SQR'],
            'wahda_sanaa' => [967108, 'وحدة صنعاء', "Al-Wahda Sana'a", 'WHS'],
            'shaab_h' => [967109, 'شعب حضرموت', 'Al-Shaab Hadramaut', 'SHH'],
            'fahman' => [967110, 'فحمان أبين', 'Fahman Abyan', 'FAH'],
            'salam' => [967111, 'سلام الغرفة', 'Salam Al-Gharfa', 'SLG'],
            'yarmuk' => [967112, 'اليرموك', 'Al-Yarmouk', 'YAR'],
            'shaab_ibb' => [967113, 'شعب إب', 'Al-Shaab Ibb', 'SHI'],
            'taliya' => [967114, 'الطليعة تعز', "Al-Tali'aa Ta'izz", 'TAL'],
            'mukalla' => [967115, 'المكلا', 'Al-Mukalla', 'MKL'],
            'sadd' => [967116, 'السد مأرب', "Al-Sadd Ma'rib", 'SDM'],
            'shabab_bayda' => [967117, 'شباب البيضاء', 'Shabab Al-Bayda', 'SHB'],
            'ittihad_h' => [967118, 'اتحاد حضرموت', 'Al-Ittihad Hadramaut', 'ITH'],
            'tilal' => [967119, 'التلال عدن', 'Al-Tilal Aden', 'TIL'],
            'wahda_aden' => [967120, 'وحدة عدن', 'Al-Wahda Aden', 'WAD'],
            'shoala' => [967121, 'الشعلة عدن', 'Al-Shoala Aden', 'SHO'],
            'shabab_jil' => [967122, 'شباب الجيل', 'Shabab Al-Jeel', 'SHJ'],
            'ain_abyan' => [967123, 'العين أبين', 'Al-Ain Abyan', 'AIN'],
            'arfan' => [967124, 'عرفان أبين', 'Arfan Abyan', 'ARF'],
            'khanfar' => [967125, 'خنفر أبين', 'Khanfar Abyan', 'KHA'],
            'husseini' => [967126, 'الحسيني لحج', 'Al-Husseini Lahj', 'HUS'],
            'ahli_taiz' => [967127, 'أهلي تعز', "Al-Ahli Ta'izz", 'AHT'],
            'rashid' => [967128, 'الرشيد تعز', "Al-Rashid Ta'izz", 'RSH'],
            'tadamun_shabwa' => [967129, 'تضامن شبوة', 'Al-Tadamun Shabwa', 'TDS'],
            'wahda_mukalla' => [967130, 'وحدة المكلا', 'Al-Wahda Al-Mukalla', 'WHM'],
            'shabab_abs' => [967131, 'شباب عبس', 'Shabab Abs', 'SHA'],
            'shaab_sanaa' => [967132, 'شعب صنعاء', "Al-Shaab Sana'a", 'SHS'],
            'may22' => [967133, '22 مايو', '22 May', '22M'],
            'fateh_dhamar' => [967134, 'الفتح ذمار', 'Al-Fateh Dhamar', 'FTD'],
            'shamsan' => [967135, 'شمسان عدن', 'Shamsan Aden', 'SHM'],
            'ahli_aden' => [967136, 'أهلي عدن', 'Al-Ahli Aden', 'AHA'],
            'azal' => [967137, 'آزال صنعاء', "Azal Sana'a", 'AZL'],
            'ahli_hudaydah' => [967138, 'أهلي الحديدة', 'Al-Ahli Al-Hudaydah', 'AHH'],
            'nasr_dali' => [967139, 'النصر الضالع', 'Al-Nasr Al-Dhale', 'NSR'],
            'shabab_mahweet' => [967140, 'شباب المحويت', 'Shabab Al-Mahweet', 'SMW'],
        ];
    }

    private function seedDivisionOne(array $teamIds, $now): void
    {
        $participants = [
            'shaab_h', 'urooba', 'mukalla', 'sadd', 'fahman', 'tadamun_h', 'ahli_sanaa',
            'wahda_sanaa', 'shabab_bayda', 'yarmuk', 'ittihad_ibb', 'hilal', 'salam', 'ittihad_h',
        ];
        $this->seedCompetitionParticipants(self::DIVISION_ONE, self::DIVISION_ONE_SEASON, $participants, $teamIds, $now);

        DB::table('stages')->updateOrInsert(
            ['id' => self::DIVISION_ONE_STAGE],
            [
                'league_id' => self::DIVISION_ONE,
                'season_id' => self::DIVISION_ONE_SEASON,
                'type_id' => 1,
                'name_ar' => 'الدوري العام',
                'name_en' => 'Regular Season',
                'type_name' => 'League',
                'sort_order' => 1,
                'finished' => false,
                'is_current' => true,
                'starting_at' => '2026-04-30 00:00:00',
                'ending_at' => '2027-03-31 23:59:59',
                'payload' => json_encode(['source' => self::FEDERATION_SOURCE, 'format' => 'double_round_robin', 'total_rounds' => 26], JSON_UNESCAPED_SLASHES),
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        $roundIds = [];
        for ($round = 1; $round <= 26; $round++) {
            $roundIds[$round] = 96726100 + $round;
            DB::table('rounds')->updateOrInsert(
                ['id' => $roundIds[$round]],
                [
                    'league_id' => self::DIVISION_ONE,
                    'season_id' => self::DIVISION_ONE_SEASON,
                    'stage_id' => self::DIVISION_ONE_STAGE,
                    'name' => 'الجولة '.$round,
                    'finished' => $round <= 11 && $round !== 10,
                    'is_current' => $round === 12,
                    'games_in_current_week' => in_array($round, [12, 13], true),
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        $fixtureId = 967260001;
        foreach ($this->divisionOneFixtures() as $fixture) {
            [$dateTime, $round, $home, $away, $homeScore, $awayScore, $status] = $fixture;
            $finished = $status === 'FT';
            DB::table('fixtures')->updateOrInsert(
                ['id' => $fixtureId++],
                [
                    'league_id' => self::DIVISION_ONE,
                    'season_id' => self::DIVISION_ONE_SEASON,
                    'round_id' => $roundIds[$round],
                    'stage_id' => self::DIVISION_ONE_STAGE,
                    'group_id' => null,
                    'home_team_id' => $teamIds[$home],
                    'away_team_id' => $teamIds[$away],
                    'starting_at' => $dateTime,
                    'state_name' => $finished ? 'Finished' : ($status === 'PST' ? 'Postponed' : 'Not Started'),
                    'state_code' => $status,
                    'home_score' => $finished ? $homeScore : null,
                    'away_score' => $finished ? $awayScore : null,
                    'is_finished' => $finished,
                    'ft_home_score' => $finished ? $homeScore : null,
                    'ft_away_score' => $finished ? $awayScore : null,
                    'minute' => $finished ? 90 : null,
                    'payload' => json_encode([
                        'source' => self::LEAGUE_SOURCE,
                        'verified_at' => '2026-08-18',
                        'timezone' => 'Asia/Aden',
                        'club_name_aliases' => ['هلال الحديدة' => 'الهلال الساحلي', 'السد' => 'السد مأرب'],
                    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        $this->syncRoundDates(self::DIVISION_ONE_SEASON, $roundIds);
        $this->seedCalculatedStandings($participants, $teamIds, $roundIds[11], $now);
    }

    private function divisionOneFixtures(): array
    {
        return [
            ['2026-04-30 15:45:00', 1, 'ittihad_ibb', 'urooba', 1, 2, 'FT'],
            ['2026-04-30 15:45:00', 1, 'fahman', 'tadamun_h', 0, 1, 'FT'],
            ['2026-04-30 15:45:00', 1, 'hilal', 'shabab_bayda', 0, 1, 'FT'],
            ['2026-04-30 15:45:00', 1, 'sadd', 'salam', 1, 0, 'FT'],
            ['2026-05-01 15:45:00', 1, 'ittihad_h', 'shaab_h', 0, 2, 'FT'],
            ['2026-05-02 15:45:00', 1, 'ahli_sanaa', 'wahda_sanaa', 1, 0, 'FT'],
            ['2026-05-02 15:45:00', 1, 'mukalla', 'yarmuk', 2, 1, 'FT'],

            ['2026-05-06 15:45:00', 2, 'sadd', 'yarmuk', 1, 1, 'FT'],
            ['2026-05-06 15:45:00', 2, 'wahda_sanaa', 'shabab_bayda', 2, 0, 'FT'],
            ['2026-05-07 15:45:00', 2, 'ahli_sanaa', 'urooba', 1, 2, 'FT'],
            ['2026-05-08 15:45:00', 2, 'ittihad_h', 'mukalla', 0, 1, 'FT'],
            ['2026-05-08 15:45:00', 2, 'salam', 'fahman', 0, 2, 'FT'],
            ['2026-05-08 15:45:00', 2, 'hilal', 'ittihad_ibb', 0, 0, 'FT'],
            ['2026-05-08 15:45:00', 2, 'shaab_h', 'tadamun_h', 1, 0, 'FT'],

            ['2026-05-13 15:45:00', 3, 'sadd', 'ahli_sanaa', 0, 0, 'FT'],
            ['2026-05-13 15:45:00', 3, 'yarmuk', 'fahman', 1, 0, 'FT'],
            ['2026-05-14 15:45:00', 3, 'salam', 'shaab_h', 0, 2, 'FT'],
            ['2026-05-14 15:45:00', 3, 'mukalla', 'shabab_bayda', 3, 0, 'FT'],
            ['2026-05-14 15:45:00', 3, 'wahda_sanaa', 'ittihad_ibb', 2, 0, 'FT'],
            ['2026-05-14 15:45:00', 3, 'hilal', 'urooba', 0, 1, 'FT'],
            ['2026-05-15 15:45:00', 3, 'tadamun_h', 'ittihad_h', 2, 1, 'FT'],

            ['2026-05-18 15:45:00', 4, 'ahli_sanaa', 'fahman', 2, 1, 'FT'],
            ['2026-05-19 15:45:00', 4, 'urooba', 'wahda_sanaa', 1, 2, 'FT'],
            ['2026-05-20 15:45:00', 4, 'shabab_bayda', 'sadd', 2, 2, 'FT'],
            ['2026-05-21 15:45:00', 4, 'hilal', 'ittihad_h', 2, 4, 'FT'],
            ['2026-05-21 15:45:00', 4, 'yarmuk', 'shaab_h', 0, 1, 'FT'],
            ['2026-05-21 15:45:00', 4, 'mukalla', 'ittihad_ibb', 1, 0, 'FT'],
            ['2026-05-22 16:00:00', 4, 'salam', 'tadamun_h', 0, 3, 'FT'],

            ['2026-06-25 16:00:00', 5, 'fahman', 'shabab_bayda', 2, 0, 'FT'],
            ['2026-06-26 16:00:00', 5, 'hilal', 'yarmuk', 0, 3, 'FT'],
            ['2026-06-26 16:00:00', 5, 'tadamun_h', 'mukalla', 0, 0, 'FT'],
            ['2026-06-27 16:00:00', 5, 'salam', 'ahli_sanaa', 0, 2, 'FT'],
            ['2026-06-27 16:00:00', 5, 'sadd', 'ittihad_ibb', 1, 0, 'FT'],
            ['2026-06-27 16:00:00', 5, 'urooba', 'ittihad_h', 1, 0, 'FT'],
            ['2026-06-28 16:00:00', 5, 'wahda_sanaa', 'shaab_h', 0, 0, 'FT'],

            ['2026-07-02 16:00:00', 6, 'tadamun_h', 'ahli_sanaa', 1, 0, 'FT'],
            ['2026-07-02 16:00:00', 6, 'urooba', 'sadd', 0, 3, 'FT'],
            ['2026-07-02 16:00:00', 6, 'hilal', 'mukalla', 1, 0, 'FT'],
            ['2026-07-03 16:00:00', 6, 'ittihad_h', 'wahda_sanaa', 2, 4, 'FT'],
            ['2026-07-03 16:00:00', 6, 'ittihad_ibb', 'fahman', 1, 1, 'FT'],
            ['2026-07-04 16:00:00', 6, 'yarmuk', 'salam', 2, 1, 'FT'],
            ['2026-07-04 16:00:00', 6, 'shaab_h', 'shabab_bayda', 3, 1, 'FT'],

            ['2026-07-07 16:00:00', 7, 'ahli_sanaa', 'mukalla', 1, 0, 'FT'],
            ['2026-07-08 16:00:00', 7, 'tadamun_h', 'wahda_sanaa', 2, 0, 'FT'],
            ['2026-07-09 16:00:00', 7, 'salam', 'ittihad_ibb', 2, 0, 'FT'],
            ['2026-07-09 16:00:00', 7, 'shaab_h', 'fahman', 1, 0, 'FT'],
            ['2026-07-09 16:00:00', 7, 'shabab_bayda', 'urooba', 1, 3, 'FT'],
            ['2026-07-10 16:00:00', 7, 'ittihad_h', 'yarmuk', 1, 2, 'FT'],
            ['2026-07-11 16:00:00', 7, 'sadd', 'hilal', 5, 2, 'FT'],

            ['2026-07-15 16:00:00', 8, 'shaab_h', 'hilal', 3, 0, 'FT'],
            ['2026-07-16 16:00:00', 8, 'yarmuk', 'ahli_sanaa', 1, 1, 'FT'],
            ['2026-07-16 16:00:00', 8, 'salam', 'shabab_bayda', 2, 1, 'FT'],
            ['2026-07-17 16:00:00', 8, 'ittihad_ibb', 'tadamun_h', 0, 2, 'FT'],
            ['2026-07-17 16:00:00', 8, 'fahman', 'ittihad_h', 2, 0, 'FT'],
            ['2026-07-17 16:00:00', 8, 'mukalla', 'urooba', 3, 0, 'FT'],
            ['2026-07-18 16:00:00', 8, 'wahda_sanaa', 'sadd', 0, 0, 'FT'],

            ['2026-07-21 16:00:00', 9, 'fahman', 'hilal', 3, 1, 'FT'],
            ['2026-07-21 16:00:00', 9, 'shaab_h', 'urooba', 1, 0, 'FT'],
            ['2026-07-22 16:00:00', 9, 'yarmuk', 'tadamun_h', 1, 1, 'FT'],
            ['2026-07-23 16:00:00', 9, 'sadd', 'mukalla', 1, 2, 'FT'],
            ['2026-07-23 16:00:00', 9, 'shabab_bayda', 'ittihad_ibb', 0, 1, 'FT'],
            ['2026-07-24 16:00:00', 9, 'ittihad_h', 'ahli_sanaa', 0, 0, 'FT'],
            ['2026-07-25 16:00:00', 9, 'salam', 'wahda_sanaa', 0, 1, 'FT'],

            ['2026-07-30 16:00:00', 10, 'mukalla', 'wahda_sanaa', 2, 3, 'FT'],
            ['2026-08-05 16:00:00', 10, 'urooba', 'fahman', 2, 2, 'FT'],
            ['2026-08-06 16:00:00', 10, 'hilal', 'salam', null, null, 'PST'],
            ['2026-08-06 16:00:00', 10, 'ahli_sanaa', 'shaab_h', 0, 0, 'FT'],
            ['2026-08-07 16:00:00', 10, 'ittihad_ibb', 'yarmuk', 2, 2, 'FT'],
            ['2026-08-07 16:00:00', 10, 'sadd', 'ittihad_h', 3, 0, 'FT'],
            ['2026-08-07 16:00:00', 10, 'tadamun_h', 'shabab_bayda', 1, 0, 'FT'],

            ['2026-08-11 16:00:00', 11, 'urooba', 'salam', 3, 1, 'FT'],
            ['2026-08-12 16:00:00', 11, 'ahli_sanaa', 'ittihad_ibb', 4, 0, 'FT'],
            ['2026-08-13 16:00:00', 11, 'ittihad_h', 'shabab_bayda', 3, 0, 'FT'],
            ['2026-08-13 16:00:00', 11, 'wahda_sanaa', 'yarmuk', 0, 0, 'FT'],
            ['2026-08-14 16:00:00', 11, 'fahman', 'sadd', 2, 0, 'FT'],
            ['2026-08-15 16:00:00', 11, 'shaab_h', 'mukalla', 3, 0, 'FT'],
            ['2026-08-16 16:00:00', 11, 'hilal', 'tadamun_h', 0, 2, 'FT'],

            ['2026-08-19 16:00:00', 12, 'urooba', 'yarmuk', null, null, 'NS'],
            ['2026-08-20 16:00:00', 12, 'mukalla', 'fahman', null, null, 'NS'],
            ['2026-08-20 16:00:00', 12, 'shabab_bayda', 'ahli_sanaa', null, null, 'NS'],
            ['2026-08-20 16:00:00', 12, 'hilal', 'wahda_sanaa', null, null, 'NS'],
            ['2026-08-21 16:00:00', 12, 'tadamun_h', 'sadd', null, null, 'NS'],
            ['2026-08-21 16:00:00', 12, 'ittihad_h', 'salam', null, null, 'NS'],
            ['2026-08-21 16:00:00', 12, 'ittihad_ibb', 'shaab_h', null, null, 'NS'],

            ['2026-08-27 16:00:00', 13, 'ittihad_h', 'ittihad_ibb', null, null, 'NS'],
            ['2026-08-27 16:00:00', 13, 'tadamun_h', 'urooba', null, null, 'NS'],
            ['2026-08-27 16:00:00', 13, 'yarmuk', 'shabab_bayda', null, null, 'NS'],
            ['2026-08-28 16:00:00', 13, 'salam', 'mukalla', null, null, 'NS'],
            ['2026-08-28 16:00:00', 13, 'shaab_h', 'sadd', null, null, 'NS'],
            ['2026-08-29 16:00:00', 13, 'ahli_sanaa', 'hilal', null, null, 'NS'],
            ['2026-08-30 16:00:00', 13, 'wahda_sanaa', 'fahman', null, null, 'NS'],
        ];
    }

    private function seedDivisionTwo(array $teamIds, $now): void
    {
        $groups = [
            1 => ['tilal', 'ahli_taiz', 'wahda_mukalla', 'arfan', 'shabab_bayda'],
            2 => ['shabab_jil', 'shaab_sanaa', 'mukalla', 'husseini', 'shabab_abs'],
            3 => ['wahda_aden', 'may22', 'ittihad_h', 'khanfar', 'fateh_dhamar'],
            4 => ['shoala', 'rashid', 'tadamun_shabwa', 'ain_abyan', 'sadd'],
        ];

        $this->seedCompetitionParticipants(
            self::DIVISION_TWO,
            self::DIVISION_TWO_SEASON,
            array_values(array_unique(array_merge(...array_values($groups)))),
            $teamIds,
            $now
        );

        DB::table('stages')->updateOrInsert(
            ['id' => self::DIVISION_TWO_STAGE],
            [
                'league_id' => self::DIVISION_TWO,
                'season_id' => self::DIVISION_TWO_SEASON,
                'type_id' => 1,
                'name_ar' => 'مرحلة التجمعات',
                'name_en' => 'Group Hubs',
                'type_name' => 'Group Stage',
                'sort_order' => 1,
                'finished' => true,
                'is_current' => false,
                'starting_at' => '2025-12-01 00:00:00',
                'ending_at' => '2026-02-28 23:59:59',
                'payload' => json_encode(['source' => self::FEDERATION_SOURCE, 'official_season_name' => '2023-2024'], JSON_UNESCAPED_SLASHES),
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        foreach ($groups as $number => $participants) {
            $id = 967240210 + $number;
            DB::table('groups')->updateOrInsert(
                ['id' => $id],
                [
                    'league_id' => self::DIVISION_TWO,
                    'season_id' => self::DIVISION_TWO_SEASON,
                    'stage_id' => self::DIVISION_TWO_STAGE,
                    'name_ar' => 'المجموعة '.$number,
                    'name_en' => 'Group '.$number,
                    'sort_order' => $number,
                    'finished' => true,
                    'is_current' => false,
                    'starting_at' => '2025-12-01 00:00:00',
                    'ending_at' => '2026-02-28 23:59:59',
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }

    private function seedCurrentCompetitionStages($now): void
    {
        DB::table('stages')->updateOrInsert(
            ['id' => self::DIVISION_THREE_STAGE],
            [
                'league_id' => self::DIVISION_THREE,
                'season_id' => self::DIVISION_THREE_SEASON,
                'type_id' => 1,
                'name_ar' => 'تصفيات المحافظات',
                'name_en' => 'Governorate Qualifiers',
                'type_name' => 'Qualifiers',
                'sort_order' => 1,
                'finished' => false,
                'is_current' => true,
                'starting_at' => '2026-06-15 00:00:00',
                'ending_at' => '2026-12-31 23:59:59',
                'payload' => json_encode([
                    'source' => 'https://yemenfa.co/post/ryys-fraa-athad-kr-alkdm-bmarb-yokd-algahzy-lantlak-tsfyat-aldrg-althalth',
                    'scope' => 'provincial_qualifiers',
                    'note' => 'Participant lists and match schedules are published separately by governorate branches.',
                ], JSON_UNESCAPED_SLASHES),
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        DB::table('stages')->updateOrInsert(
            ['id' => self::REPUBLIC_CUP_STAGE],
            [
                'league_id' => self::REPUBLIC_CUP,
                'season_id' => self::REPUBLIC_CUP_SEASON,
                'type_id' => 2,
                'name_ar' => 'الأدوار الإقصائية',
                'name_en' => 'Knockout Rounds',
                'type_name' => 'Knockout',
                'sort_order' => 1,
                'finished' => false,
                'is_current' => true,
                'starting_at' => '2026-04-20 00:00:00',
                'ending_at' => '2026-12-31 23:59:59',
                'payload' => json_encode([
                    'source' => 'https://yemenfa.co/post/agtmaaa-mosaa-bryas-alaaysy-obhdor-mmthly-38-nadya-akrar-tagyl-dory-aldrg-alaol-al-30-abryl-oalarbaaaaa-kraa-kas-algmhory',
                    'participants' => 40,
                    'format' => 'knockout',
                ], JSON_UNESCAPED_SLASHES),
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );
    }

    private function seedCompetitionParticipants(int $leagueId, int $seasonId, array $participants, array $teamIds, $now): void
    {
        foreach ($participants as $key) {
            DB::table('active_seasons')->updateOrInsert(
                ['team_id' => $teamIds[$key], 'season_id' => $seasonId, 'league_id' => $leagueId],
                ['updated_at' => $now, 'created_at' => $now]
            );
        }
    }

    private function seedCalculatedStandings(array $participants, array $teamIds, int $roundId, $now): void
    {
        $table = [];
        foreach ($participants as $key) {
            $table[$key] = ['played' => 0, 'won' => 0, 'draw' => 0, 'lost' => 0, 'gf' => 0, 'ga' => 0, 'points' => 0];
        }

        foreach ($this->divisionOneFixtures() as $fixture) {
            [, , $home, $away, $homeScore, $awayScore, $status] = $fixture;
            if ($status !== 'FT') {
                continue;
            }

            $table[$home]['played']++;
            $table[$away]['played']++;
            $table[$home]['gf'] += $homeScore;
            $table[$home]['ga'] += $awayScore;
            $table[$away]['gf'] += $awayScore;
            $table[$away]['ga'] += $homeScore;

            if ($homeScore > $awayScore) {
                $table[$home]['won']++;
                $table[$away]['lost']++;
                $table[$home]['points'] += 3;
            } elseif ($homeScore < $awayScore) {
                $table[$away]['won']++;
                $table[$home]['lost']++;
                $table[$away]['points'] += 3;
            } else {
                $table[$home]['draw']++;
                $table[$away]['draw']++;
                $table[$home]['points']++;
                $table[$away]['points']++;
            }
        }

        uasort($table, static function (array $left, array $right): int {
            return [$right['points'], $right['gf'] - $right['ga'], $right['gf']]
                <=> [$left['points'], $left['gf'] - $left['ga'], $left['gf']];
        });

        $position = 1;
        foreach ($table as $key => $row) {
            DB::table('standings')->updateOrInsert(
                ['id' => 967260500 + $position],
                [
                    'league_id' => self::DIVISION_ONE,
                    'season_id' => self::DIVISION_ONE_SEASON,
                    'stage_id' => self::DIVISION_ONE_STAGE,
                    'round_id' => $roundId,
                    'group_id' => null,
                    'team_id' => $teamIds[$key],
                    'participant_id' => $teamIds[$key],
                    'group_name' => null,
                    'standing_type' => 'total',
                    'position' => $position,
                    'points' => $row['points'],
                    'played' => $row['played'],
                    'won' => $row['won'],
                    'draw' => $row['draw'],
                    'lost' => $row['lost'],
                    'goals_for' => $row['gf'],
                    'goals_against' => $row['ga'],
                    'goal_difference' => $row['gf'] - $row['ga'],
                    'payload_json' => json_encode(['source' => self::LEAGUE_SOURCE, 'through' => '2026-08-16'], JSON_UNESCAPED_SLASHES),
                    'synced_at' => $now,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
            $position++;
        }
    }

    private function syncRoundDates(int $seasonId, array $roundIds): void
    {
        foreach ($roundIds as $roundId) {
            $range = DB::table('fixtures')
                ->where('season_id', $seasonId)
                ->where('round_id', $roundId)
                ->selectRaw('MIN(starting_at) AS starts_at, MAX(starting_at) AS ends_at')
                ->first();

            if ($range && $range->starts_at) {
                DB::table('rounds')->where('id', $roundId)->update([
                    'starting_at' => $range->starts_at,
                    'ending_at' => $range->ends_at,
                ]);
            }
        }
    }
}
