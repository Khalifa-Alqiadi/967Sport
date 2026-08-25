<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class Sport967PlatformSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $now = now();

            if (!DB::table('settings')->where('id', 1)->exists()) {
                throw new RuntimeException('The main settings row (ID 1) must exist before running this seeder.');
            }

            DB::table('settings')->where('id', 1)->update([
                'site_title_ar' => '967Sport | منصة الرياضة اليمنية',
                'site_title_en' => '967Sport | Yemen Sports Platform',
                'site_desc_ar' => 'منصة 967Sport لمتابعة كرة القدم والرياضة اليمنية: أخبار، نتائج، انتقالات، منتخبات وأندية.',
                'site_desc_en' => '967Sport covers Yemeni football and sport: news, results, transfers, national teams and clubs.',
                'site_keywords_ar' => '967Sport، الرياضة اليمنية، الدوري اليمني، المنتخب اليمني، كرة القدم اليمنية، كأس الجمهورية، أندية اليمن',
                'site_keywords_en' => '967Sport, Yemen sport, Yemeni League, Yemen national team, Yemeni football, Republic Cup',
                'contact_t1_ar' => 'اليمن',
                'contact_t1_en' => 'Yemen',
                'contact_t3' => null,
                'contact_t4' => null,
                'contact_t5' => null,
                'contact_t6' => null,
                'contact_t7_ar' => 'منصة الرياضة اليمنية؛ ننقل الخبر والنتيجة وقصة اللاعب من قلب الملعب اليمني.',
                'contact_t7_en' => 'Yemen sports platform, covering news, results and the stories behind the game.',
                'social_links' => json_encode([
                    ['url' => 'https://www.facebook.com/967spt', 'icon' => 'fab fa-facebook-f', 'title' => 'Facebook', 'row_id' => '1', 'row_no' => '1', 'color' => '#4D2C85'],
                    ['url' => 'https://x.com/967spt', 'icon' => 'fab fa-x-twitter', 'title' => 'X', 'row_id' => '2', 'row_no' => '2', 'color' => '#111111'],
                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'updated_by' => 1,
                'updated_at' => $now,
            ]);

            DB::table('webmaster_settings')->where('id', 1)->update([
                'latest_news_section_id' => 3,
                'home_content1_section_id' => 3,
                'tags_status' => 1,
                'timezone' => 'Asia/Aden',
                'updated_by' => 1,
                'updated_at' => $now,
            ]);

            DB::table('webmaster_sections')->where('id', 3)->update([
                'title_ar' => 'أخبار 967Sport',
                'title_en' => '967Sport News',
                'sections_status' => 1,
                'tags_status' => 1,
                'date_status' => 1,
                'featured_status' => 1,
                'sportmonks_status' => 3,
                'seo_title_ar' => 'أخبار الرياضة وكرة القدم اليمنية',
                'seo_title_en' => 'Yemeni Football and Sports News',
                'seo_description_ar' => 'آخر أخبار الدوري اليمني والمنتخبات والأندية والمحترفين وكأس الجمهورية.',
                'seo_description_en' => 'Latest Yemeni league, national teams, clubs, professionals and Republic Cup news.',
                'seo_keywords_ar' => 'الدوري اليمني، المنتخب اليمني، أندية اليمن، 967Sport',
                'seo_keywords_en' => 'Yemeni League, Yemen national team, Yemen clubs, 967Sport',
                'seo_url_slug_ar' => 'الأخبار',
                'seo_url_slug_en' => 'news',
                'updated_by' => 1,
                'updated_at' => $now,
            ]);

            $categories = [
                30 => ['الدوري اليمني', 'Yemeni League', 'الدوري-اليمني', 'yemeni-league'],
                31 => ['المنتخبات الوطنية', 'National Teams', 'المنتخبات-الوطنية', 'national-teams'],
                32 => ['كأس الجمهورية', 'Republic Cup', 'كأس-الجمهورية', 'republic-cup'],
                33 => ['المحترفون اليمنيون', 'Yemeni Professionals', 'المحترفون-اليمنيون', 'yemeni-professionals'],
                34 => ['انتقالات الأندية', 'Club Transfers', 'انتقالات-الأندية', 'club-transfers'],
                35 => ['الملاعب والمنشآت', 'Stadiums and Facilities', 'الملاعب-والمنشآت', 'stadiums-facilities'],
                36 => ['رياضات يمنية', 'Yemeni Sports', 'رياضات-يمنية', 'yemeni-sports'],
            ];

            foreach ($categories as $id => [$ar, $en, $slugAr, $slugEn]) {
                DB::table('sections')->updateOrInsert(
                    ['id' => $id],
                    [
                        'webmaster_id' => 3,
                        'father_id' => 0,
                        'row_no' => $id - 29,
                        'title_ar' => $ar,
                        'title_en' => $en,
                        'status' => 1,
                        'visits' => 0,
                        'seo_title_ar' => $ar.' | 967Sport',
                        'seo_title_en' => $en.' | 967Sport',
                        'seo_url_slug_ar' => $slugAr,
                        'seo_url_slug_en' => $slugEn,
                        'created_by' => 1,
                        'updated_by' => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }
            DB::table('sections')->where('webmaster_id', 3)->whereNotIn('id', array_keys($categories))->update(['status' => 0]);

            DB::table('topics')->where('id', 1)->update([
                'title_ar' => 'عن 967Sport',
                'title_en' => 'About 967Sport',
                'details_ar' => '<p><strong>967Sport</strong> منصة رياضية يمنية تتابع كرة القدم المحلية والمنتخبات والأندية والمحترفين، وتضع النتيجة والخبر والقصة الرياضية في مكان واحد.</p>',
                'details_en' => '<p><strong>967Sport</strong> is a Yemeni sports platform covering domestic football, national teams, clubs and Yemeni professionals.</p>',
                'seo_url_slug_ar' => 'عن-967sport',
                'seo_url_slug_en' => 'about-967sport',
                'updated_by' => 1,
                'updated_at' => $now,
            ]);
            DB::table('topics')->where('id', 2)->update([
                'title_ar' => 'تواصل مع 967Sport',
                'title_en' => 'Contact 967Sport',
                'seo_url_slug_ar' => 'تواصل-معنا',
                'seo_url_slug_en' => 'contact-us',
                'updated_by' => 1,
                'updated_at' => $now,
            ]);

            $welcomeContent = [
                'title_ar' => '967Sport.. منصة الرياضة اليمنية',
                'desc_ar' => 'من قلب الملاعب اليمنية',
                'details_ar' => '<h1>الرياضة اليمنية في مكان واحد</h1><p>نتابع الدوري اليمني والمنتخبات الوطنية وكأس الجمهورية وانتقالات الأندية والمحترفين اليمنيين، بمحتوى سريع ودقيق وقريب من الجمهور.</p>',
                'bg_ar' => null,
                'title_en' => '967Sport — Yemen Sports Platform',
                'desc_en' => 'From the heart of Yemeni sport',
                'details_en' => '<h1>Yemeni sport in one place</h1><p>Coverage of the Yemeni League, national teams, Republic Cup, transfers and Yemeni professionals.</p>',
                'bg_en' => null,
                'banner_area_id' => '6',
            ];
            DB::table('topic_blocks')->where('id', 6)->update([
                'row_no' => 4,
                'block_name' => '967Sport Introduction',
                'content' => json_encode($welcomeContent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'status' => 1,
                'css_classes' => 'sport-manifesto-block',
                'updated_by' => 1,
                'updated_at' => $now,
            ]);
            DB::table('topic_blocks')->where('id', 7)->update(['row_no' => 6, 'updated_by' => 1, 'updated_at' => $now]);

            DB::table('topic_blocks')->updateOrInsert(
                ['id' => 9673299],
                [
                    'topic_id' => 5,
                    'row_no' => 2,
                    'block_name' => '967Sport Featured News Ticker',
                    'type' => 3,
                    'content' => json_encode([
                        'title_ar' => 'الآن',
                        'desc_ar' => 'NEWSROOM 967',
                        'bg_ar' => null,
                        'title_en' => 'Now',
                        'desc_en' => '967 NEWSROOM',
                        'bg_en' => null,
                        'module_id' => '3',
                        'category_ids' => null,
                        'records_count' => '5',
                        'records_order' => '2',
                        'view_style' => 'News',
                    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'title_status' => 1,
                    'desc_status' => 1,
                    'image_status' => 0,
                    'divider_status' => 0,
                    'more_btn_status' => 0,
                    'bg_color' => null,
                    'css_classes' => 'sport-featured-ticker-block',
                    'status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            DB::table('topic_blocks')->updateOrInsert(
                ['id' => 9673300],
                [
                    'topic_id' => 5,
                    'row_no' => 3,
                    'block_name' => '967Sport Gateway Banners',
                    'type' => 2,
                    'content' => json_encode([
                        'title_ar' => 'كل الرياضة اليمنية بلمسة واحدة',
                        'desc_ar' => 'انتقل مباشرة إلى البطولة أو المنتخب أو القصة التي تهمك، وتابع آخر المستجدات من قلب الحدث.',
                        'bg_ar' => null,
                        'title_en' => 'All Yemeni sport, one touch away',
                        'desc_en' => 'Go straight to the competition, national team or story that matters to you, and follow the latest action.',
                        'bg_en' => null,
                        'banner_area_id' => '5',
                        'banner_style' => 'banners',
                    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'title_status' => 1,
                    'desc_status' => 1,
                    'image_status' => 0,
                    'divider_status' => 0,
                    'more_btn_status' => 0,
                    'bg_color' => null,
                    'css_classes' => 'sport-gateway-block',
                    'status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            DB::table('topic_blocks')->updateOrInsert(
                ['id' => 9673301],
                [
                    'topic_id' => 5,
                    'row_no' => 5,
                    'block_name' => 'Latest 967Sport News',
                    'type' => 3,
                    'content' => json_encode([
                        'title_ar' => 'آخر الأخبار',
                        'desc_ar' => 'أبرز ما يحدث في الرياضة اليمنية',
                        'bg_ar' => null,
                        'title_en' => 'Latest News',
                        'desc_en' => 'The latest from Yemeni sport',
                        'bg_en' => null,
                        'module_id' => '3',
                        'category_ids' => null,
                        'records_count' => '7',
                        'records_order' => '1',
                        'view_style' => 'News',
                    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'title_status' => 1,
                    'desc_status' => 1,
                    'image_status' => 0,
                    'divider_status' => 0,
                    'more_btn_status' => 1,
                    'bg_color' => null,
                    'css_classes' => 'section-bg',
                    'status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            DB::table('menus')->whereIn('father_id', [1, 2, 4, 18])->update(['status' => 0]);
            foreach ([
                [9673401, 1, 1, 'الرئيسية', 'Home', '/', 0],
                [9673402, 1, 2, 'الأخبار', 'News', '/الأخبار', 0],
                [9673403, 1, 3, 'فيسبوك', 'Facebook', 'https://www.facebook.com/967spt', 1],
                [9673411, 2, 1, 'الرئيسية', 'Home', '/', 0],
                [9673412, 2, 2, 'عن 967Sport', 'About 967Sport', '/عن-967sport', 0],
                [9673413, 2, 3, 'الأخبار', 'News', '/الأخبار', 0],
            ] as [$id, $father, $row, $ar, $en, $link, $target]) {
                DB::table('menus')->updateOrInsert(
                    ['id' => $id],
                    [
                        'father_id' => $father,
                        'row_no' => $row,
                        'title_ar' => $ar,
                        'title_en' => $en,
                        'status' => 1,
                        'type' => 1,
                        'cat_id' => 0,
                        'link' => $link,
                        'target' => $target,
                        'created_by' => 1,
                        'updated_by' => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }
        });

        Cache::flush();
    }
}
