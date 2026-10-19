<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $banners = [
            9673501 => ['الدوري اليمني', 'Yemeni League', 'bi-trophy', route('sport.league', ['league' => 1], false), route('sport.localized.league', ['lang' => 'en', 'league' => 1], false)],
            9673502 => ['كل البطولات', 'All Competitions', 'bi-grid', route('sport.competitions', [], false), route('sport.localized.competitions', ['lang' => 'en'], false)],
            9673503 => ['كأس الجمهورية', 'Republic Cup', 'bi-award', route('sport.league', ['league' => 96704], false), route('sport.localized.league', ['lang' => 'en', 'league' => 96704], false)],
            9673504 => ['مباريات اليوم', "Today's Matches", 'bi-calendar-event', route('sport.matches', ['day' => 'today'], false), route('sport.localized.matches', ['lang' => 'en', 'day' => 'today'], false)],
            9673505 => ['مباريات الغد', "Tomorrow's Matches", 'bi-calendar-plus', route('sport.matches', ['day' => 'tomorrow'], false), route('sport.localized.matches', ['lang' => 'en', 'day' => 'tomorrow'], false)],
            9673506 => ['نتائج الأمس', "Yesterday's Results", 'bi-bar-chart-line', route('sport.matches', ['day' => 'yesterday'], false), route('sport.localized.matches', ['lang' => 'en', 'day' => 'yesterday'], false)],
            9673601 => ['الدوري اليمني', 'Yemeni League', 'bi-trophy', route('sport.league', ['league' => 1], false), route('sport.localized.league', ['lang' => 'en', 'league' => 1], false)],
            9673602 => ['البطولات', 'Competitions', 'bi-grid', route('sport.competitions', [], false), route('sport.localized.competitions', ['lang' => 'en'], false)],
            9673603 => ['النتائج', 'Results', 'bi-bar-chart-line', route('sport.matches', ['day' => 'yesterday'], false), route('sport.localized.matches', ['lang' => 'en', 'day' => 'yesterday'], false)],
            9673604 => ['المباريات', 'Matches', 'bi-calendar-event', route('sport.matches', [], false), route('sport.localized.matches', ['lang' => 'en'], false)],
        ];

        foreach ($banners as $id => [$titleAr, $titleEn, $icon, $linkAr, $linkEn]) {
            DB::table('banners')->where('id', $id)->update([
                'title_ar' => $titleAr,
                'title_en' => $titleEn,
                'icon' => $icon,
                'link_ar' => $linkAr,
                'link_en' => $linkEn,
                'link_url' => $linkAr,
            ]);
        }

        Cache::forget('_Loader_BannersList');
    }

    public function down(): void
    {
        // Banner links are editable content; rolling back must not replace later editor changes.
    }
};
