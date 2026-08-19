<?php

namespace Database\Seeders;

use App\Helpers\Helper;
use App\Models\Topic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class Sport967SliderSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            if (!DB::table('webmaster_banners')->where('id', 1)->exists()) {
                throw new RuntimeException('The home banner section (ID 1) must exist before running this seeder.');
            }

            $now = now();
            $slides = [
                [9673201, 1, 'كل تفاصيل الدوري اليمني في مكان واحد', 'Yemeni League — all in one place', 'نتائج المباريات، جدول الترتيب، أخبار الأندية والجولات أولًا بأول.', 'Matches, standings, clubs and every round in one place.', '967-slider-league.svg', 9673105],
                [9673202, 2, 'منتخبات اليمن.. الحلم الذي يجمعنا', 'Yemen national teams — one shared dream', 'متابعة المنتخبات الوطنية من الإعداد وحتى صافرة الحسم.', 'Follow Yemen national teams from preparation to the final whistle.', '967-slider-yemen.svg', 9673101],
                [9673203, 3, '967Sport.. منصة الرياضة اليمنية', '967Sport — Yemen Sports Platform', 'خبر موثوق، نتيجة سريعة، وقصة يمنية من قلب الملعب.', 'Trusted news, fast results and Yemeni stories from the heart of the game.', '967-slider-platform.svg', null],
            ];
            $ids = array_column($slides, 0);
            DB::table('banners')->where('section_id', 1)->whereNotIn('id', $ids)->update(['status' => 0]);

            foreach ($slides as [$id, $row, $titleAr, $titleEn, $detailsAr, $detailsEn, $file, $topicId]) {
                $topic = $topicId ? Topic::find($topicId) : null;
                DB::table('banners')->updateOrInsert(
                    ['id' => $id],
                    [
                        'section_id' => 1,
                        'fixture_id' => $topic?->fixture_id,
                        'row_no' => $row,
                        'title_ar' => $titleAr,
                        'title_en' => $titleEn,
                        'details_ar' => $detailsAr,
                        'details_en' => $detailsEn,
                        'file_ar' => $file,
                        'file_en' => $file,
                        'link_ar' => $topic ? $this->relativeTopicUrl($topic, 'ar') : 'https://www.facebook.com/967spt',
                        'link_en' => $topic ? $this->relativeTopicUrl($topic, 'en') : 'https://www.facebook.com/967spt',
                        'link_url' => 'https://www.facebook.com/967spt',
                        'status' => 1,
                        'visits' => 0,
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

    private function relativeTopicUrl(Topic $topic, string $language): string
    {
        $url = Helper::topicURL($topic->id, $language, $topic);
        $path = parse_url($url, PHP_URL_PATH) ?: '/';
        $query = parse_url($url, PHP_URL_QUERY);

        return $path.($query ? '?'.$query : '');
    }
}
