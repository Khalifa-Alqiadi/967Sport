<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class Sport967NewsSeeder extends Seeder
{
    private const SOURCE = 'https://www.facebook.com/967spt';

    public function run(): void
    {
        DB::transaction(function (): void {
            if (!DB::table('webmaster_sections')->where('id', 3)->exists()) {
                throw new RuntimeException('The news webmaster section (ID 3) must exist before running this seeder.');
            }

            $now = now();
            foreach ($this->articles() as $row => $article) {
                $id = 9673100 + $row;
                $fixtureId = $this->fixtureId($article['fixture'] ?? null);

                DB::table('topics')->updateOrInsert(
                    ['id' => $id],
                    [
                        'webmaster_id' => 3,
                        'section_id' => 0,
                        'row_no' => $row,
                        'title_ar' => $article['title_ar'],
                        'title_en' => $article['title_en'],
                        'details_ar' => '<p>'.$article['details_ar'].'</p><p><strong>المصدر:</strong> حساب 967Sport.</p>',
                        'details_en' => '<p>'.$article['details_en'].'</p><p><strong>Source:</strong> 967Sport.</p>',
                        'date' => $article['date'],
                        'photo_file' => $article['image'],
                        'source' => self::SOURCE,
                        'status' => 1,
                        'featured' => $article['featured'] ?? 0,
                        'visits' => 0,
                        'league_id' => $article['league_id'] ?? null,
                        'team_id' => $article['team_id'] ?? null,
                        'season_id' => $article['season_id'] ?? null,
                        'fixture_id' => $fixtureId,
                        'seo_title_ar' => $article['title_ar'].' | 967Sport',
                        'seo_title_en' => $article['title_en'].' | 967Sport',
                        'seo_description_ar' => mb_substr($article['details_ar'], 0, 165),
                        'seo_description_en' => mb_substr($article['details_en'], 0, 165),
                        'seo_keywords_ar' => $article['keywords_ar'],
                        'seo_keywords_en' => $article['keywords_en'],
                        'seo_url_slug_ar' => $article['slug_ar'],
                        'seo_url_slug_en' => $article['slug_en'],
                        'created_by' => 1,
                        'updated_by' => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );

                DB::table('topic_categories')->where('topic_id', $id)->delete();
                DB::table('topic_categories')->insert([
                    'topic_id' => $id,
                    'section_id' => $article['category_id'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        });

        Cache::flush();
    }

    private function fixtureId(?array $fixture): ?int
    {
        if (!$fixture) {
            return null;
        }

        return DB::table('fixtures')
            ->where('season_id', 9672601)
            ->where('home_team_id', $fixture[0])
            ->where('away_team_id', $fixture[1])
            ->whereDate('starting_at', $fixture[2])
            ->value('id');
    }

    private function articles(): array
    {
        return [
            1 => [
                'title_ar' => 'خبر | منتخب الشباب يخسر وديته أمام 22 مايو',
                'title_en' => 'Yemen youth team loses friendly against 22 May',
                'details_ar' => 'خسر المنتخب الوطني للشباب مباراته التجريبية أمام نادي 22 مايو، ضمن برنامجه الإعدادي لتصفيات كأس آسيا المقبلة، في محطة تهدف لاختبار الجاهزية الفنية قبل الاستحقاق القاري.',
                'details_en' => 'Yemen youth national team lost a friendly against 22 May as part of its preparations for the upcoming Asian Cup qualifiers.',
                'date' => '2026-08-18', 'image' => '967-news-national.svg', 'category_id' => 31, 'featured' => 1,
                'slug_ar' => 'منتخب-الشباب-يخسر-وديته-أمام-22-مايو', 'slug_en' => 'yemen-youth-friendly-22-may',
                'keywords_ar' => 'منتخب اليمن للشباب، 22 مايو، تصفيات كأس آسيا', 'keywords_en' => 'Yemen youth, 22 May, Asian Cup qualifiers',
            ],
            2 => [
                'title_ar' => 'وحدة صنعاء يعود من المكلا بانتصار مثير',
                'title_en' => "Al-Wahda Sana'a earns dramatic win in Mukalla",
                'details_ar' => 'حقق وحدة صنعاء فوزًا ثمينًا خارج ملعبه على المكلا بثلاثة أهداف مقابل هدفين في افتتاح الجولة العاشرة، ليعزز حضوره في سباق مراكز المقدمة.',
                'details_en' => "Al-Wahda Sana'a defeated Al-Mukalla 3–2 away from home in the opening match of round ten.",
                'date' => '2026-07-30', 'image' => '967-news-league.svg', 'category_id' => 30, 'featured' => 1,
                'league_id' => 1, 'team_id' => 967108, 'season_id' => 9672601, 'fixture' => [967115, 967108, '2026-07-30'],
                'slug_ar' => 'وحدة-صنعاء-يهزم-المكلا-بثلاثية', 'slug_en' => 'wahda-sanaa-beats-mukalla-3-2',
                'keywords_ar' => 'وحدة صنعاء، المكلا، الدوري اليمني', 'keywords_en' => "Al-Wahda Sana'a, Al-Mukalla, Yemeni League",
            ],
            3 => [
                'title_ar' => 'اليرموك والتضامن يقتسمان نقاط المواجهة',
                'title_en' => 'Al-Yarmouk and Al-Tadamun share the points',
                'details_ar' => 'حسم التعادل بهدف لمثله مواجهة اليرموك وتضامن حضرموت في الجولة التاسعة؛ تقدم التضامن بهدف عكسي قبل أن يعيد شهاب عطاء اليرموك إلى المباراة.',
                'details_en' => 'Al-Yarmouk and Al-Tadamun Hadramaut drew 1–1 in round nine after Al-Yarmouk recovered from an own-goal opener.',
                'date' => '2026-07-22', 'image' => '967-news-league.svg', 'category_id' => 30,
                'league_id' => 1, 'team_id' => 967112, 'season_id' => 9672601, 'fixture' => [967112, 967102, '2026-07-22'],
                'slug_ar' => 'تعادل-اليرموك-وتضامن-حضرموت', 'slug_en' => 'yarmouk-tadamun-draw',
                'keywords_ar' => 'اليرموك، تضامن حضرموت، الدوري اليمني', 'keywords_en' => 'Al-Yarmouk, Al-Tadamun Hadramaut, Yemeni League',
            ],
            4 => [
                'title_ar' => 'التعادل السلبي يحسم لقاء اتحاد حضرموت وأهلي صنعاء',
                'title_en' => "Ittihad Hadramaut holds Al-Ahli Sana'a",
                'details_ar' => 'انتهت مواجهة اتحاد حضرموت وأهلي صنعاء دون أهداف، بعد مباراة شهدت فرصًا متبادلة لم ينجح الفريقان في ترجمتها إلى أهداف.',
                'details_en' => "Ittihad Hadramaut and Al-Ahli Sana'a finished goalless after both sides failed to convert their chances.",
                'date' => '2026-07-24', 'image' => '967-news-league.svg', 'category_id' => 30,
                'league_id' => 1, 'team_id' => 967118, 'season_id' => 9672601, 'fixture' => [967118, 967101, '2026-07-24'],
                'slug_ar' => 'تعادل-اتحاد-حضرموت-وأهلي-صنعاء', 'slug_en' => 'ittihad-hadramaut-ahli-sanaa-draw',
                'keywords_ar' => 'اتحاد حضرموت، أهلي صنعاء، الدوري اليمني', 'keywords_en' => "Ittihad Hadramaut, Al-Ahli Sana'a, Yemeni League",
            ],
            5 => [
                'title_ar' => 'شعب حضرموت يحلق في الصدارة بثلاثية',
                'title_en' => 'Al-Shaab Hadramaut strengthens lead with three goals',
                'details_ar' => 'واصل شعب حضرموت عروضه القوية وحسم مواجهة الهلال الساحلي بثلاثية نظيفة، معززًا صدارته ومؤكدًا حضوره كأحد أبرز المنافسين على اللقب.',
                'details_en' => 'Al-Shaab Hadramaut defeated Al-Hilal Al-Sahely 3–0 to reinforce its lead in the Yemeni League.',
                'date' => '2026-07-15', 'image' => '967-news-league.svg', 'category_id' => 30, 'featured' => 1,
                'league_id' => 1, 'team_id' => 967109, 'season_id' => 9672601, 'fixture' => [967109, 967105, '2026-07-15'],
                'slug_ar' => 'شعب-حضرموت-يهزم-الهلال-بثلاثية', 'slug_en' => 'shaab-hadramaut-beats-hilal',
                'keywords_ar' => 'شعب حضرموت، الهلال الساحلي، صدارة الدوري', 'keywords_en' => 'Al-Shaab Hadramaut, Al-Hilal, league leaders',
            ],
            6 => [
                'title_ar' => 'بارادم يتجدد بهوية بصرية ومرافق حديثة',
                'title_en' => 'Baradem Stadium enters a new redevelopment phase',
                'details_ar' => 'بدأت أعمال تحديث ملعب الفقيد بارادم في المكلا، وتشمل إعادة التعشيب وشاشة عملاقة ومنظومة إضاءة حديثة وتأهيل المرافق وغرف اللاعبين والحكام.',
                'details_en' => 'Baradem Stadium in Mukalla is being upgraded with a new pitch, giant screen, modern lighting and renovated player and referee facilities.',
                'date' => '2026-07-23', 'image' => '967-news-stadium.svg', 'category_id' => 35,
                'slug_ar' => 'تحديث-ملعب-الفقيد-بارادم', 'slug_en' => 'baradem-stadium-redevelopment',
                'keywords_ar' => 'ملعب بارادم، المكلا، ملاعب اليمن', 'keywords_en' => 'Baradem Stadium, Mukalla, Yemen stadiums',
            ],
            7 => [
                'title_ar' => 'عبدالواسع المطري يبدأ محطة عراقية مع ديالى',
                'title_en' => 'Abdulwasea Al-Matari joins Iraqi club Diyala',
                'details_ar' => 'أعلن نادي ديالى العراقي تعاقده مع قائد المنتخب الوطني عبدالواسع المطري، ليخوض اللاعب تجربة احترافية جديدة في الملاعب العراقية.',
                'details_en' => 'Iraqi club Diyala announced the signing of Yemen captain Abdulwasea Al-Matari for a new professional spell in Iraq.',
                'date' => '2026-07-20', 'image' => '967-news-transfer.svg', 'category_id' => 33,
                'slug_ar' => 'عبدالواسع-المطري-ينضم-إلى-ديالى', 'slug_en' => 'abdulwasea-al-matari-joins-diyala',
                'keywords_ar' => 'عبدالواسع المطري، ديالى، المحترفون اليمنيون', 'keywords_en' => 'Abdulwasea Al-Matari, Diyala, Yemeni professionals',
            ],
            8 => [
                'title_ar' => 'أحمد علي قاسم يعتذر عن الاستمرار مع اتحاد إب',
                'title_en' => 'Ahmed Ali Qasem steps down at Al-Ittihad Ibb',
                'details_ar' => 'اعتذر المدرب أحمد علي قاسم عن مواصلة مهمته مع اتحاد إب بعد سلسلة من النتائج السلبية في الدوري اليمني، لتنتهي تجربته مع الفريق.',
                'details_en' => 'Coach Ahmed Ali Qasem stepped down from Al-Ittihad Ibb following a difficult run of Yemeni League results.',
                'date' => '2026-07-18', 'image' => '967-news-transfer.svg', 'category_id' => 34,
                'league_id' => 1, 'team_id' => 967104, 'season_id' => 9672601,
                'slug_ar' => 'أحمد-علي-قاسم-يغادر-اتحاد-إب', 'slug_en' => 'ahmed-ali-qasem-leaves-ittihad-ibb',
                'keywords_ar' => 'أحمد علي قاسم، اتحاد إب، مدرب', 'keywords_en' => 'Ahmed Ali Qasem, Al-Ittihad Ibb, coach',
            ],
            9 => [
                'title_ar' => 'عمار الكلدي مدربًا لوحدة عدن',
                'title_en' => 'Ammar Al-Kaldi appointed Al-Wahda Aden coach',
                'details_ar' => 'أعلن وحدة عدن تعيين عمار الكلدي مديرًا فنيًا للفريق الأول، مع استمرار ماجد الطعسلي مساعدًا للمدرب ضمن الجهاز الفني الجديد.',
                'details_en' => 'Al-Wahda Aden appointed Ammar Al-Kaldi as first-team head coach, with Majed Al-Taasli continuing as assistant.',
                'date' => '2026-07-12', 'image' => '967-news-transfer.svg', 'category_id' => 34,
                'team_id' => 967120,
                'slug_ar' => 'عمار-الكلدي-مدربا-لوحدة-عدن', 'slug_en' => 'ammar-al-kaldi-wahda-aden-coach',
                'keywords_ar' => 'عمار الكلدي، وحدة عدن، انتقالات المدربين', 'keywords_en' => 'Ammar Al-Kaldi, Al-Wahda Aden, coach',
            ],
            10 => [
                'title_ar' => 'ذهب وفضة لليمن في البطولة العربية للجودو',
                'title_en' => 'Yemen wins gold and silver at Arab judo championship',
                'details_ar' => 'تألق صدام حسين الجراح بذهبية وزن 27 كجم، وحصد عبيدة حسين الجراح فضية وزن 38 كجم، في حضور يمني لافت بالبطولة العربية للأندية والمنتخبات في الأردن.',
                'details_en' => 'Saddam Hussein Al-Jarrah won 27kg gold and Obeida Hussein Al-Jarrah claimed 38kg silver at the Arab judo championship in Jordan.',
                'date' => '2026-07-25', 'image' => '967-news-medal.svg', 'category_id' => 36,
                'slug_ar' => 'ذهب-وفضة-لليمن-في-الجودو-العربي', 'slug_en' => 'yemen-gold-silver-arab-judo',
                'keywords_ar' => 'الجودو اليمني، صدام الجراح، عبيدة الجراح', 'keywords_en' => 'Yemen judo, Saddam Al-Jarrah, Obeida Al-Jarrah',
            ],
        ];
    }
}
