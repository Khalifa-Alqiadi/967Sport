<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Sport967NavigationBannersSeeder extends Seeder
{
    private const GATEWAY_GROUP_ID = 5;
    private const MANIFESTO_GROUP_ID = 6;

    public function run(): void
    {
        DB::transaction(function (): void {
            $now = now();

            $this->seedGroup(
                self::GATEWAY_GROUP_ID,
                5,
                'بوابات 967Sport الرئيسية',
                '967Sport Main Gateways',
                $now
            );
            $this->seedGroup(
                self::MANIFESTO_GROUP_ID,
                6,
                'روابط تعريف المنصة',
                'Platform Introduction Links',
                $now
            );

            $gatewayBanners = [
                [9673501, 1, 'الدوري اليمني', 'Yemeni League', 'المباريات والترتيب والإحصائيات.', 'Fixtures, standings and statistics.', 'bi-trophy', route('sport.league', ['league' => 1], false), route('sport.localized.league', ['lang' => 'en', 'league' => 1], false)],
                [9673502, 2, 'كل البطولات', 'All Competitions', 'استعرض البطولات اليمنية ومواسمها.', 'Explore Yemeni competitions and seasons.', 'bi-grid', route('sport.competitions', [], false), route('sport.localized.competitions', ['lang' => 'en'], false)],
                [9673503, 3, 'كأس الجمهورية', 'Republic Cup', 'المباريات والنتائج والترتيب.', 'Fixtures, results and standings.', 'bi-award', route('sport.league', ['league' => 96704], false), route('sport.localized.league', ['lang' => 'en', 'league' => 96704], false)],
                [9673504, 4, 'مباريات اليوم', "Today's Matches", 'جدول مباريات اليوم ونتائجها.', "Today's fixtures and results.", 'bi-calendar-event', route('sport.matches', ['day' => 'today'], false), route('sport.localized.matches', ['lang' => 'en', 'day' => 'today'], false)],
                [9673505, 5, 'مباريات الغد', "Tomorrow's Matches", 'تعرّف إلى المواجهات القادمة.', 'See the upcoming fixtures.', 'bi-calendar-plus', route('sport.matches', ['day' => 'tomorrow'], false), route('sport.localized.matches', ['lang' => 'en', 'day' => 'tomorrow'], false)],
                [9673506, 6, 'نتائج الأمس', "Yesterday's Results", 'راجع نتائج آخر المباريات.', 'Review the latest results.', 'bi-bar-chart-line', route('sport.matches', ['day' => 'yesterday'], false), route('sport.localized.matches', ['lang' => 'en', 'day' => 'yesterday'], false)],
            ];

            foreach ($gatewayBanners as [$id, $row, $titleAr, $titleEn, $detailsAr, $detailsEn, $icon, $linkAr, $linkEn]) {
                $this->seedBanner(
                    $id,
                    self::GATEWAY_GROUP_ID,
                    $row,
                    $titleAr,
                    $titleEn,
                    $detailsAr,
                    $detailsEn,
                    $icon,
                    $linkAr,
                    $linkEn,
                    $now
                );
            }

            $manifestoBanners = [
                [9673601, 1, 'الدوري اليمني', 'Yemeni League', 'bi-trophy', route('sport.league', ['league' => 1], false), route('sport.localized.league', ['lang' => 'en', 'league' => 1], false)],
                [9673602, 2, 'البطولات', 'Competitions', 'bi-grid', route('sport.competitions', [], false), route('sport.localized.competitions', ['lang' => 'en'], false)],
                [9673603, 3, 'النتائج', 'Results', 'bi-bar-chart-line', route('sport.matches', ['day' => 'yesterday'], false), route('sport.localized.matches', ['lang' => 'en', 'day' => 'yesterday'], false)],
                [9673604, 4, 'المباريات', 'Matches', 'bi-calendar-event', route('sport.matches', [], false), route('sport.localized.matches', ['lang' => 'en'], false)],
            ];

            foreach ($manifestoBanners as [$id, $row, $titleAr, $titleEn, $icon, $linkAr, $linkEn]) {
                $this->seedBanner(
                    $id,
                    self::MANIFESTO_GROUP_ID,
                    $row,
                    $titleAr,
                    $titleEn,
                    null,
                    null,
                    $icon,
                    $linkAr,
                    $linkEn,
                    $now
                );
            }

            DB::table('banners')
                ->where('section_id', self::GATEWAY_GROUP_ID)
                ->whereNotIn('id', array_column($gatewayBanners, 0))
                ->update(['status' => 0, 'updated_at' => $now]);

            DB::table('banners')
                ->where('section_id', self::MANIFESTO_GROUP_ID)
                ->whereNotIn('id', array_column($manifestoBanners, 0))
                ->update(['status' => 0, 'updated_at' => $now]);
        });

        Cache::forget('_Loader_BannersList');
    }

    private function seedGroup(int $id, int $row, string $titleAr, string $titleEn, $now): void
    {
        DB::table('webmaster_banners')->updateOrInsert(
            ['id' => $id],
            [
                'row_no' => $row,
                'title_ar' => $titleAr,
                'title_en' => $titleEn,
                'width' => 0,
                'height' => 0,
                'desc_status' => 1,
                'link_status' => 1,
                'type' => 0,
                'icon_status' => 1,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }

    private function seedBanner(
        int $id,
        int $groupId,
        int $row,
        string $titleAr,
        string $titleEn,
        ?string $detailsAr,
        ?string $detailsEn,
        string $icon,
        string $linkAr,
        string $linkEn,
        $now
    ): void {
        DB::table('banners')->updateOrInsert(
            ['id' => $id],
            [
                'section_id' => $groupId,
                'row_no' => $row,
                'title_ar' => $titleAr,
                'title_en' => $titleEn,
                'details_ar' => $detailsAr,
                'details_en' => $detailsEn,
                'icon' => $icon,
                'link_ar' => $linkAr,
                'link_en' => $linkEn,
                'link_url' => $linkAr,
                'status' => 1,
                'visits' => 0,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }

}
