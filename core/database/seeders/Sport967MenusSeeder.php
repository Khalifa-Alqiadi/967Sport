<?php

namespace Database\Seeders;

use App\Models\WebmasterSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Sport967MenusSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $settings = WebmasterSetting::query()->first();
            $headerMenuId = (int) ($settings?->header_menu_id ?: 1);
            $footerMenuId = (int) ($settings?->footer_menu_id ?: 2);
            $now = now();

            $headerLinks = [
                [9673401, 1, 'الرئيسية', 'Home', '/', '/', 'fa-home'],
                [9673441, 2, 'البطولات', 'Competitions', 'competitions', 'competitions', 'fa-trophy'],
                [9673442, 3, 'المباريات', 'Matches', 'matches', 'matches', 'fa-calendar'],
                [9673402, 4, 'الأخبار', 'News', 'الأخبار', 'news', 'fa-newspaper-o'],
            ];

            $footerLinks = [
                [9673411, 1, 'الرئيسية', 'Home', '/', '/', 'fa-home'],
                [9673451, 2, 'البطولات', 'Competitions', 'competitions', 'competitions', 'fa-trophy'],
                [9673452, 3, 'المباريات', 'Matches', 'matches', 'matches', 'fa-calendar'],
                [9673413, 4, 'الأخبار', 'News', 'الأخبار', 'news', 'fa-newspaper-o'],
                [9673412, 5, 'عن 967Sport', 'About 967Sport', 'عن-967sport', 'about-967sport', 'fa-info-circle'],
            ];

            $this->seedLinks($headerMenuId, $headerLinks, $now);
            $this->seedLinks($footerMenuId, $footerLinks, $now);
        });

        Cache::flush();
    }

    private function seedLinks(int $menuId, array $links, $now): void
    {
        foreach ($links as [$id, $row, $titleAr, $titleEn, $linkAr, $linkEn, $icon]) {
            DB::table('menus')->updateOrInsert(
                ['id' => $id],
                [
                    'father_id' => $menuId,
                    'row_no' => $row,
                    'title_ar' => $titleAr,
                    'title_en' => $titleEn,
                    'status' => 1,
                    'type' => 1,
                    'cat_id' => 0,
                    'link' => $linkAr,
                    'link_ar' => $linkAr,
                    'link_en' => $linkEn,
                    'icon' => $icon,
                    'target' => 0,
                    'created_by' => 1,
                    'updated_by' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
