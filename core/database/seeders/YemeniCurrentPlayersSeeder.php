<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class YemeniCurrentPlayersSeeder extends Seeder
{
    private const SEASON_ID = 9672601;
    private const SPORT_ID = 1;
    private const VERIFIED_AT = '2026-08-24';

    /**
     * Seed the publicly documented players of the 2025/2026 Yemeni Division One.
     *
     * The Yemeni FA does not currently publish one complete public squad list for
     * every club. Therefore the seeder combines current squad indexes with the
     * federation's current-season match reports. It deliberately does not invent
     * dates of birth, shirt numbers or positions that were not published.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $this->guardDependencies();

            $countries = DB::table('countries')->pluck('id', 'code');
            $yemenId = $countries->get('YE');

            if (!$yemenId) {
                throw new RuntimeException('Yemen (YE) must exist in countries before running this seeder.');
            }

            $now = now();

            foreach ($this->squads() as $teamId => $squad) {
                foreach ($squad['players'] as $index => $player) {
                    $playerId = $this->playerId($teamId, $index + 1);
                    $nationalityId = $countries->get($player[5] ?? 'YE', $yemenId);
                    $source = $player[6] ?? $squad['source'];
                    $evidence = $player[7] ?? $squad['evidence'];
                    $position = $player[4] ?? null;

                    $payload = [
                        'dataset' => '967sport-yemeni-division-one-2025-2026',
                        'source_url' => $source,
                        'source_season' => '2025/2026',
                        'evidence' => $evidence,
                        'position_label' => $position,
                        'verified_at' => self::VERIFIED_AT,
                        'notes' => 'Unpublished values are intentionally left null.',
                    ];

                    DB::table('players')->updateOrInsert(
                        ['id' => $playerId],
                        [
                            'name_ar' => $player[0],
                            'name_en' => $player[1],
                            'common_name' => $player[1],
                            'image_path' => null,
                            'date_of_birth' => $player[2] ?: null,
                            'gender' => 'male',
                            'height' => null,
                            'weight' => null,
                            'country_id' => $nationalityId,
                            'nationality_id' => $nationalityId,
                            'position_id' => null,
                            'detailed_position_id' => null,
                            'foot' => null,
                            'sport_id' => self::SPORT_ID,
                            'payload_json' => $this->json($payload),
                            'updated_at' => $now,
                            'created_at' => $now,
                        ]
                    );

                    DB::table('team_players')->updateOrInsert(
                        [
                            'team_id' => $teamId,
                            'player_id' => $playerId,
                            'season_id' => self::SEASON_ID,
                        ],
                        [
                            'position_id' => null,
                            'detailed_position_id' => null,
                            'jersey_number' => null,
                            'from_date' => '2026-04-30',
                            'to_date' => null,
                            'is_current' => true,
                            'is_captain' => false,
                            'transfer_id' => null,
                            'payload_json' => $this->json($payload + ['team_id' => $teamId]),
                            'updated_at' => $now,
                            'created_at' => $now,
                        ]
                    );
                }
            }
        });
    }

    private function guardDependencies(): void
    {
        if (!DB::table('seasons')->where('id', self::SEASON_ID)->exists()) {
            throw new RuntimeException(
                'Season 9672601 is missing. Run YemeniCurrentCompetitionsSeeder first.'
            );
        }

        $requiredTeams = array_keys($this->squads());
        $existingTeams = DB::table('teams')->whereIn('id', $requiredTeams)->pluck('id')->all();
        $missingTeams = array_values(array_diff($requiredTeams, $existingTeams));

        if ($missingTeams !== []) {
            throw new RuntimeException(
                'Required Yemeni teams are missing: '.implode(', ', $missingTeams)
                .'. Run YemeniCurrentCompetitionsSeeder first.'
            );
        }
    }

    /**
     * Reserved deterministic IDs: 9675 + three-digit team suffix + two-digit row.
     * New names must be appended to a club list so existing IDs remain stable.
     */
    private function playerId(int $teamId, int $row): int
    {
        return 967500000 + (($teamId - 967000) * 100) + $row;
    }

    private function json(array $value): string
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }

    /**
     * Player row: [Arabic name, English name, DOB, unused, position, country code,
     * optional source URL, optional evidence label].
     */
    private function squads(): array
    {
        $fa = 'federation-match-report';

        return [
            967101 => [
                'source' => 'https://yemenfa.co/post/ahly-snaaaaa-yksb-shbab-albydaaa-fhman-yfoz-aal-almkla-oalhlal-ytgaoz-slam-alghrf',
                'evidence' => $fa,
                'players' => [
                    ['جلال الجلال', 'Galal Al-Galal', '1996-01-03', null, 'Midfielder', 'YE'],
                    ['يوسف الحيمي', 'Yousef Al-Haimi', null, null, null, 'YE'],
                    ['صقر الدربي', 'Saqr Al-Darbi', null, null, null, 'YE', 'https://www.aden-tm.net/news/369634'],
                    ['زكريا تفتوف', 'Zakaria Taftoof', null, null, 'Forward', 'YE', 'https://en.wikipedia.org/wiki/2025%E2%80%9326_Yemeni_League', 'current-season-scorer-index'],
                ],
            ],
            967102 => [
                'source' => 'https://www.national-football-teams.com/club/5679/2025_1/Al_Tadamun_Hadramawt.html',
                'evidence' => '2025/2026-squad-index',
                'players' => [
                    ['رضوان الحبيشي', 'Radhawan Al-Hubaishi', '1993-07-03', null, 'Centre Back', 'YE'],
                    ['ممدوح بن عجاج', 'Mamdooh Ban Agag', '2003-08-26', null, 'Centre Back', 'YE'],
                    ['شانسيل نداي', 'Chancel Ndaye', '1999-04-14', null, 'Centre Back', 'BI'],
                    ['نادر سهل', 'Nader Sahal', '1990-01-01', null, 'Centre Back', 'YE'],
                    ['أميدي ماساسي', 'Amedé Masasi', '1991-09-11', null, 'Defensive Midfielder', 'CD'],
                    ['عادل عباس قاسم', 'Adel Abbas Qasem', '2008-03-01', null, 'Attacking Midfielder', 'YE'],
                    ['عمر الجولان', 'Omar Al-Golan', null, null, 'Midfielder', 'YE', 'https://yemenfa.co/post/tdamn-hdrmot-yksb-slam-alghrf-bthlathy-fy-khtam-algol-alrabaa-lldory', $fa],
                    ['عبدالعزيز مصنوم', 'Abdulaziz Masnom', '2006-02-06', null, 'Left Winger', 'YE'],
                    ['يزيد اليزيدي', 'Yazid Al-Yazidi', null, null, null, 'YE', 'https://yemenfa.co/post/tdamn-hdrmot-yksb-slam-alghrf-bthlathy-fy-khtam-algol-alrabaa-lldory', $fa],
                ],
            ],
            967103 => [
                'source' => 'https://tribuna.com/en/clubs/al-oruba-zabid/squad/',
                'evidence' => 'current-squad-index',
                'players' => [
                    ['طلال علي الحبيشي', 'Talal Ali Ali Al Hubaishi', null, null, 'Goalkeeper', 'YE'],
                    ['محمد علي مساعد الدغيش', 'Mohammed Ali Musaeed Al Dughish', null, null, 'Defender', 'YE'],
                    ['ألاو فتاي أديسا', 'Alao Fatai Adisa', null, null, 'Defender', 'NG'],
                    ['عبدالكريم حسين الوشاح', 'Abdulkarem Hussein Mohammed Al Weshah', null, null, 'Defender', 'YE'],
                    ['عمار عمر زيدان', 'Ammar Omar Mohammed Zaidan', null, null, 'Defender', 'YE'],
                    ['أمين أحمد الصبحي', 'Ameen Ahmed Saleh Al Sabahi', null, null, 'Defender', 'YE'],
                    ['عدنان أحمد الكباتي', 'Adnan Ahmed Ali Abdulatef Al Kubati', null, null, 'Defender', 'YE'],
                    ['وسيم أحمد القور', 'Wasim Ahmed Abdullah Al Qor', null, null, 'Defender', 'YE'],
                    ['عارف ثابت الدالي', 'Aref Thabit Mohammed Al Dali', null, null, 'Defender', 'YE'],
                    ['أيمن سعد المطري', 'Aiman Saad Saleh Al Matari', null, null, 'Defender', 'YE'],
                    ['أحمد علي الظاهري', 'Ahmed Ali Al Dhaheri', null, null, 'Defender', 'YE'],
                    ['هشام عبد الواسع الأصبحي', 'Hesham Abdulwasea Saeed Al Asbahi', null, null, 'Midfielder', 'YE'],
                    ['كمال علي الحمداني', 'Kamal Ali Musaeed Al Hamdani', null, null, 'Midfielder', 'YE'],
                    ['هلال محمد البخيتي', 'Helal Mohammed Saeed Al Bukhaiti', null, null, 'Midfielder', 'YE'],
                    ['جمال أحمد الذبياني', 'Gamal Ahmed Al Dhaibani', null, null, 'Midfielder', 'YE'],
                    ['هيثم عبده الأصبحي', 'Haitham Abdo Saeed Thabit Al Asbahi', null, null, 'Forward', 'YE'],
                    ['عبدالإله يحيى شريان', 'Abdulelah Yahya Hamood Sharyan', null, null, 'Forward', 'YE'],
                    ['شعبان مصطفى النجار', 'Shaaban Mostafa Ali Naggar', null, null, 'Forward', 'YE'],
                    ['ياسر علي الجبر', 'Yaser Ali Hasan Ali Al Gabr', null, null, 'Forward', 'YE'],
                    ['أمجد الرضا', 'Amjad Al-Rida', null, null, null, 'YE', 'https://yemenfa.co/post/ohd-snaaaaa-yksb-alaarob-oydkhl-dayr-almnafs-aal-alsdar', $fa],
                ],
            ],
            967104 => [
                'source' => 'https://www.national-football-teams.com/club/8388/2025_1/Al_Ittihad_Ibb.html',
                'evidence' => '2025/2026-squad-index',
                'players' => [
                    ['أنور العوج', 'Anwar Al-Aug', '1986-02-05', null, 'Goalkeeper', 'YE'],
                    ['فضل العرومي', 'Fadhl Al-Aroomi', '1981-12-13', null, 'Midfielder', 'YE'],
                    ['عبدالكريم القطوي', 'Abdulkarem Al-Qetwi', '1987-02-06', null, 'Midfielder', 'YE'],
                    ['نشوان الهجام', 'Nashwan Al-Haggam', '1983-10-23', null, 'Forward', 'YE'],
                    ['محمد وهيب', 'Mohammed Wahib', null, null, null, 'YE', 'https://yemenfa.co/post/tdamn-hdrmot-ytgaoz-shbab-albydaaa-alsd-yksb-athad-hdrmot-otaaadl-athad-ab-oalyrmok', $fa],
                    ['محمود فارع', 'Mahmoud Fare', null, null, null, 'YE', 'https://yemenfa.co/post/tdamn-hdrmot-ytgaoz-shbab-albydaaa-alsd-yksb-athad-hdrmot-otaaadl-athad-ab-oalyrmok', $fa],
                ],
            ],
            967105 => [
                'source' => 'https://tribuna.com/en/clubs/al-helal-al-sahely/squad/2025-2026/',
                'evidence' => '2025/2026-squad-index',
                'players' => [
                    ['محمد علي عياش', 'Mohammed Ali Ayash', '1986-03-06', null, 'Goalkeeper', 'YE'],
                    ['سالم عبدالله عوض سعيد', 'Salem Abdullah Awadh Saeed', '1984-01-01', null, 'Goalkeeper', 'YE'],
                    ['عبدالله محمد الصافي', 'Abdullah Mohammed Abdullah Al Safi', '1986-02-26', null, 'Defender', 'YE'],
                    ['أمجد عبدالله خيران', 'Amgad Abdullah Khiran Mohammed', null, null, 'Defender', 'YE'],
                    ['سالم سعيد بالحمّر', 'Salem Saeed Abdullah Ba Lhamar', '1977-05-02', null, 'Defender', 'YE'],
                    ['بيبي نزيلينجي ميسانو', 'Pepe Nzelenge Misano', null, null, 'Defender', 'CD'],
                    ['عمر محمد المرشدي', 'Omar Mohammed Murad Al Munshedi', null, null, 'Defender', 'YE'],
                    ['نزار ناصر رزق', 'Nezar Nasser Salem Rezq', '1987-01-15', null, 'Defender', 'YE'],
                    ['أنس صالح يوسف سالم', 'Anas Saleh Yousef Salem', null, null, 'Defender', 'SD'],
                    ['مهند حسن منصر', 'Mohanad Hasan Rageh Munassar', '1986-03-15', null, 'Midfielder', 'YE'],
                    ['مبو إيزا', 'Mbo Iza', null, null, 'Midfielder', 'CD'],
                    ['علي محمد مبارك يوسف', 'Ali Mohammed Mubarak Yousef', null, null, 'Midfielder', 'YE'],
                    ['منصر عوض باحاج', 'Munassar Awadh Abdullah Ba Haj', null, null, 'Midfielder', 'YE'],
                    ['صالح أحمد الشهري', 'Saleh Ahmed Qasem Al Shehri', null, null, 'Midfielder', 'YE'],
                    ['أكرم كامل السلوي', 'Akram Kamel Ali Al Selwi', '1986-09-08', null, 'Forward', 'YE'],
                    ['بيداسو هورا فييسا', 'Bedaso Hora Feyisa', null, null, 'Forward', 'ET'],
                    ['نسيم الشريف', 'Naseem Al-Sharif', null, null, null, 'YE', 'https://yemenfa.co/post/almkla-oshaab-hdrmot-yoaslan-sdar-aldory-alymny', $fa],
                    ['عبادي باري', 'Abadi Bari', null, null, null, 'YE', 'https://yemenfa.co/post/almkla-oshaab-hdrmot-yoaslan-sdar-aldory-alymny', $fa],
                    ['حسن عياش', 'Hassan Ayash', null, null, null, 'YE', 'https://yemenfa.co/post/ahly-snaaaaa-yksb-shbab-albydaaa-fhman-yfoz-aal-almkla-oalhlal-ytgaoz-slam-alghrf', $fa],
                    ['حسن مهند', 'Hassan Mohannad', null, null, null, 'YE', 'https://yemenfa.co/post/ahly-snaaaaa-yksb-shbab-albydaaa-fhman-yfoz-aal-almkla-oalhlal-ytgaoz-slam-alghrf', $fa],
                ],
            ],
            967108 => [
                'source' => 'https://www.national-football-teams.com/club/3136/2025_1/Al_Wahda_Sana_A.html',
                'evidence' => '2025/2026-squad-index',
                'players' => [
                    ['عمار البيضاني', 'Ammar Al-Baidani', '2001-10-30', null, 'Defender', 'YE'],
                    ['عبدالمعين الجرشي', 'Abdul Muain Al-Jarshi', '1994-01-01', null, 'Defender', 'YE'],
                    ['هيثم ثابت الأصبحي', 'Haitham Thabit Al-Asbahi', '1986-02-06', null, 'Right Midfielder', 'YE'],
                    ['محمد التيري', 'Mohammed Al-Tiri', '2000-02-04', null, 'Centre Midfielder', 'YE'],
                    ['محمد الغمري', 'Mohammed Al-Ghamri', null, null, 'Midfielder', 'YE'],
                    ['عبدالغني الغرابي', 'Abdul Ghani Al-Ghurabi', '1978-07-01', null, 'Midfielder', 'YE'],
                    ['إبراهيم الكحالي', 'Ebrahim Al-Kuhali', '1981-11-15', null, 'Midfielder', 'YE'],
                    ['أحمد عبدالله علوس', 'Ahmed Abdullah Alos', '1994-04-03', null, 'Midfielder', 'YE'],
                    ['عبدالعزيز خميس', 'Abdulaziz Khamis', null, null, 'Midfielder', 'YE'],
                    ['صلاح سعيد', 'Salah Saeed', '1991-01-04', null, 'Midfielder', 'YE'],
                    ['عبدالله شريان', 'Abdullah Sharyan', '1986-01-11', null, 'Midfielder', 'YE'],
                    ['قاسم الشرفي', 'Gassem Al-Sharafi', '2004-10-15', null, 'Forward', 'YE'],
                    ['محمد البتول', 'Mohammed Al-Batoul', null, null, 'Defender', 'YE', 'https://yemenfa.co/post/ohd-snaaaaa-yksb-alaarob-oydkhl-dayr-almnafs-aal-alsdar', $fa],
                    ['عبدالرحمن الشامي', 'Abdulrahman Al-Shami', null, null, null, 'YE', 'https://yemenfa.co/post/ohd-snaaaaa-yksb-alaarob-oydkhl-dayr-almnafs-aal-alsdar', $fa],
                ],
            ],
            967109 => [
                'source' => 'https://www.national-football-teams.com/club/7664/2025_1/Al_Sha_Ab_Hadramawt.html',
                'evidence' => '2025/2026-squad-index',
                'players' => [
                    ['محمد أمان خير الله', 'Mohammed Aman Khairalah', '1997-04-14', null, 'Goalkeeper', 'YE'],
                    ['سالم مطران', 'Salem Matran', '1995-09-26', null, 'Defender', 'YE'],
                    ['منصر باحاج', 'Munassar Ba Haj', '1990-01-01', null, 'Centre Midfielder', 'YE'],
                    ['محمد فوزي باحميد', 'Mohammed Fawzi Ba Hamid', '1997-08-01', null, 'Centre Midfielder', 'YE'],
                    ['سالم موسى عمر', 'Salem Mousa Omar Abdulmanea', null, null, 'Midfielder', 'YE'],
                    ['علي عوض العمقي', 'Ali Awad Al-Omqi', '1982-07-02', null, 'Midfielder', 'YE'],
                    ['عماد منصور', 'Emad Mansoor', '1992-04-15', null, 'Centre Forward', 'YE'],
                    ['محسن قراوي', 'Mohsen Qarawi', '1989-05-15', null, 'Forward', 'YE'],
                    ['حيدر أسلم', 'Haider Aslam', '1994-07-11', null, 'Attacking Midfielder', 'YE', 'https://yemenfa.co/post/sdar-thlathy-fy-khtam-algol-althany-ldory-aldrg-alaol', $fa],
                    ['وحيد الخياط', 'Wahid Al-Khayat', '1986-01-01', null, 'Centre Midfielder', 'YE', 'https://yemenfa.co/post/almkla-oshaab-hdrmot-yoaslan-sdar-aldory-alymny', $fa],
                ],
            ],
            967110 => [
                'source' => 'https://www.national-football-teams.com/club/32417/2026_1/Fahman_Abyan.html',
                'evidence' => '2026-squad-index',
                'players' => [
                    ['عبدالله السعدي', 'Abdullah Al-Saadi', '2002-04-23', null, 'Goalkeeper', 'YE'],
                    ['الخضر الدوح', 'Al-Khader Al-Douh', '2004-11-01', null, 'Right Back', 'YE'],
                    ['محمد القشمي', 'Mohammed Al-Qashmi', '2005-10-07', null, 'Centre Back', 'YE'],
                    ['حمزة هنش', 'Hamzah Hanash', '2002-01-28', null, 'Centre Midfielder', 'YE'],
                    ['وجدي الرطيل', 'Wajdi Al-Rutail', null, null, null, 'YE', 'https://yemenfa.co/post/ahly-snaaaaa-yksb-shbab-albydaaa-fhman-yfoz-aal-almkla-oalhlal-ytgaoz-slam-alghrf', $fa],
                    ['محمد دمبر', 'Mohammed Dambar', null, null, null, 'YE', 'https://yemenfa.co/post/sdar-thlathy-fy-khtam-algol-althany-ldory-aldrg-alaol', $fa],
                    ['علي سالم', 'Ali Salem', null, null, null, 'YE', 'https://yemenfa.co/post/sdar-thlathy-fy-khtam-algol-althany-ldory-aldrg-alaol', $fa],
                ],
            ],
            967111 => [
                'source' => 'https://www.aden-tm.net/news/369634',
                'evidence' => $fa,
                'players' => [
                    ['هاني برك إدريس', 'Hani Barak Idris', null, null, null, 'YE'],
                    ['محمد حسن الجابري', 'Mohammed Hassan Al-Jabri', null, null, null, 'YE'],
                ],
            ],
            967112 => [
                'source' => 'https://tribuna.com/en/clubs/al-yarmuk-al-rawda-sana/squad/2025-2026/',
                'evidence' => '2025/2026-squad-index',
                'players' => [
                    ['مروان بسباس', 'Marwan Besbas', null, null, 'Goalkeeper', 'YE'],
                    ['عبدالقوي سيلان', 'Abdulqawi Sailan', null, null, 'Defender', 'YE'],
                    ['إبراهيم جهامة', 'Ebrahim Gehamah', null, null, 'Defender', 'YE'],
                    ['محمد عبدالرحمن', 'Mohammed Abdulrahman', null, null, 'Defender', 'YE'],
                    ['صالح عمر محمد', 'Saleh Omar Mohammed', null, null, 'Defender', 'YE'],
                    ['عصام محمد أحمد عون', 'Essam Mohammed Ahmed Awn', null, null, 'Defender', 'YE'],
                    ['حمزة السرابي', 'Hamzah Al-Surabi', '2006-12-06', null, 'Left Back', 'YE'],
                    ['مفيد جمال', 'Mufeed Gamal', '2000-10-01', null, 'Left Back', 'YE'],
                    ['أسامة عنبر', 'Osama Anbar', null, null, 'Centre Midfielder', 'YE'],
                    ['محمود عيش يحيى', 'Mahmoud Aish Yahya', null, null, 'Midfielder', 'YE'],
                    ['أحمد نبيل ذبعان', 'Ahmed Nabil Dhabaan', '1994-04-21', null, 'Left Midfielder', 'YE'],
                    ['خيري يوسف الشيباني', 'Khairi Yousef Alwan Al Shaibani', null, null, 'Midfielder', 'YE'],
                    ['محمد عبدالله العبيدي', 'Mohammed Abdullah Mohammed Al Abidi', null, null, 'Forward', 'YE'],
                    ['مراد مصلح العامري', 'Murad Mosalh Al Amri', null, null, 'Forward', 'YE'],
                    ['منذر عبدالله علي', 'Munther Abdullah Ali', null, null, 'Forward', 'YE'],
                    ['أليكس نويزي', 'Alex Nweze', null, null, 'Forward', 'NG'],
                    ['أسامة الصعفاني', 'Osama Al-Safani', null, null, null, 'YE', 'https://yemenfa.co/post/tdamn-hdrmot-ytgaoz-shbab-albydaaa-alsd-yksb-athad-hdrmot-otaaadl-athad-ab-oalyrmok', $fa],
                    ['عبدالجليل نجاد', 'Abduljalil Najad', null, null, null, 'YE', 'https://yemenfa.co/post/tdamn-hdrmot-ytgaoz-shbab-albydaaa-alsd-yksb-athad-hdrmot-otaaadl-athad-ab-oalyrmok', $fa],
                    ['شهاب عطاء', 'Shehab Ata', null, null, null, 'YE', 'https://www.aden-tm.net/news/369634', $fa],
                ],
            ],
            967115 => [
                'source' => 'https://en.wikipedia.org/wiki/2025%E2%80%9326_Yemeni_League',
                'evidence' => 'current-season-scorer-index',
                'players' => [
                    ['حسن باصفر', 'Hassan Ba Safar', null, null, 'Forward', 'YE'],
                    ['أحمد الحسني', 'Ahmed Al-Hassani', null, null, null, 'YE', 'https://yemenfa.co/post/almkla-yhsm-sdar-tgmaa-alhdyd-oykhtf-btak-altahl-lldrg-alaol', $fa],
                    ['سعيد العولقي', 'Saeed Al-Awlaki', null, null, null, 'YE', 'https://yemenfa.co/post/almkla-yhsm-sdar-tgmaa-alhdyd-oykhtf-btak-altahl-lldrg-alaol', $fa],
                    ['محمد القدسي', 'Mohammed Al-Qudsi', null, null, null, 'YE', 'https://yemenfa.co/post/almkla-oshaab-hdrmot-yoaslan-sdar-aldory-alymny', $fa],
                    ['أكرم جوبح', 'Akram Jobah', null, null, null, 'YE', 'https://yemenfa.co/post/sdar-thlathy-fy-khtam-algol-althany-ldory-aldrg-alaol', $fa],
                ],
            ],
            967116 => [
                'source' => 'https://yemenfa.co/post/shbab-albydaaa-oalsd-marb-yktfyan-baltaaadl-fy-algol-alrabaa-lldory',
                'evidence' => $fa,
                'players' => [
                    ['مصعب المزري', 'Musab Al-Mazri', null, null, null, 'YE'],
                    ['محمد أبو هدعش', 'Mohammed Abu Hadaash', null, null, null, 'YE'],
                    ['علاء عوشة', 'Alaa Ousha', null, null, null, 'YE'],
                    ['حسن ميسرة', 'Hassan Maisara', null, null, null, 'YE', 'https://yemenfa.co/post/tdamn-hdrmot-ytgaoz-shbab-albydaaa-alsd-yksb-athad-hdrmot-otaaadl-athad-ab-oalyrmok'],
                    ['عبدالله وهان', 'Abdullah Wahan', null, null, null, 'YE', 'https://yemenfa.co/post/tdamn-hdrmot-ytgaoz-shbab-albydaaa-alsd-yksb-athad-hdrmot-otaaadl-athad-ab-oalyrmok'],
                ],
            ],
            967117 => [
                'source' => 'https://yemenfa.co/post/shbab-albydaaa-oalsd-marb-yktfyan-baltaaadl-fy-algol-alrabaa-lldory',
                'evidence' => $fa,
                'players' => [
                    ['علي العبيدي', 'Ali Al-Obaidi', null, null, null, 'YE'],
                    ['ياسين حسين الشرفي', 'Yaseen Hussein Al-Sharafi', null, null, null, 'YE', 'https://www.aden-tm.net/news/369634'],
                ],
            ],
            967118 => [
                'source' => 'https://yemenfa.co/post/almkla-oshaab-hdrmot-yoaslan-sdar-aldory-alymny',
                'evidence' => $fa,
                'players' => [
                    ['عبدالله عبدالقادر', 'Abdullah Abdulqader', null, null, null, 'YE'],
                    ['مسلم عبدالإله', 'Muslim Abdulah', null, null, null, 'YE'],
                    ['أحمد جلال', 'Ahmed Jalal', null, null, null, 'YE'],
                    ['عصام وديع', 'Issam Wadi', null, null, null, 'YE', 'https://yemenfa.co/post/athad-hdrmot-ytgaoz-22-mayo-oytsdr-tgmaa-syyon-ofth-thmar-yhkk-fozh-alaol'],
                ],
            ],
        ];
    }
}
