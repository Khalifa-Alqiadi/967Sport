<?php

namespace Database\Seeders;

use App\Helpers\Helper;
use App\Models\Section;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use RuntimeException;

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

            $sections = Section::query()->whereIn('id', [30, 31, 32, 33, 34, 35])->get()->keyBy('id');
            $missingSections = collect([30, 31, 32, 33, 34, 35])->diff($sections->keys());

            if ($missingSections->isNotEmpty()) {
                throw new RuntimeException(
                    'Missing 967Sport news sections: '.$missingSections->implode(', ')
                    .'. Run Sport967PlatformSeeder first.'
                );
            }

            $gatewayBanners = [
                [9673501, 1, 30, 'الدوري اليمني', 'Yemeni League', 'كل أخبار وجولات وترتيب الدوري اليمني.', 'League news, rounds and standings.', 'bi-trophy'],
                [9673502, 2, 31, 'المنتخبات الوطنية', 'National Teams', 'متابعة المنتخبات اليمنية في مختلف الفئات.', 'Follow Yemen national teams at every level.', 'bi-flag'],
                [9673503, 3, 32, 'كأس الجمهورية', 'Republic Cup', 'مباريات وأخبار بطولة كأس الجمهورية.', 'Republic Cup matches and news.', 'bi-award'],
                [9673504, 4, 33, 'المحترفون اليمنيون', 'Yemeni Professionals', 'أخبار اللاعبين اليمنيين المحترفين خارجياً.', 'Yemeni professionals playing abroad.', 'bi-person-badge'],
                [9673505, 5, 34, 'انتقالات الأندية', 'Club Transfers', 'آخر الانتقالات والتعاقدات بين الأندية.', 'Latest club transfers and signings.', 'bi-arrow-left-right'],
                [9673506, 6, 35, 'الملاعب والمنشآت', 'Stadiums and Facilities', 'أخبار الملاعب والمنشآت الرياضية اليمنية.', 'Yemeni stadiums and sports facilities.', 'bi-building'],
            ];

            foreach ($gatewayBanners as [$id, $row, $sectionId, $titleAr, $titleEn, $detailsAr, $detailsEn, $icon]) {
                $section = $sections->get($sectionId);
                $this->seedBanner(
                    $id,
                    self::GATEWAY_GROUP_ID,
                    $row,
                    $titleAr,
                    $titleEn,
                    $detailsAr,
                    $detailsEn,
                    $icon,
                    $this->relativeCategoryUrl($section, 'ar'),
                    $this->relativeCategoryUrl($section, 'en'),
                    $now
                );
            }

            $manifestoBanners = [
                [9673601, 1, 'الدوري اليمني', 'Yemeni League', 'bi-trophy', $sections->get(30)],
                [9673602, 2, 'المنتخبات', 'National Teams', 'bi-flag', $sections->get(31)],
                [9673603, 3, 'النتائج', 'Results', 'bi-bar-chart-line', null],
                [9673604, 4, 'المحترفون', 'Professionals', 'bi-person-badge', $sections->get(33)],
            ];

            foreach ($manifestoBanners as [$id, $row, $titleAr, $titleEn, $icon, $section]) {
                $linkAr = $section ? $this->relativeCategoryUrl($section, 'ar') : '/#home-fixtures-title';
                $linkEn = $section ? $this->relativeCategoryUrl($section, 'en') : '/en#home-fixtures-title';

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

    private function relativeCategoryUrl(Section $section, string $language): string
    {
        $url = Helper::categoryURL($section->id, $language, $section);
        $path = parse_url($url, PHP_URL_PATH) ?: '/';
        $query = parse_url($url, PHP_URL_QUERY);

        return $path.($query ? '?'.$query : '');
    }
}
