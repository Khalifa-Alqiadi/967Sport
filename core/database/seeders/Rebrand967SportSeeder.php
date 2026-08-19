<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Rebrand967SportSeeder extends Seeder
{
    public function run(): void
    {
        $socialLinks = [
            [
                'url' => 'https://www.facebook.com/967spt',
                'icon' => 'fab fa-facebook-f',
                'title' => 'Facebook',
                'row_id' => '1',
                'row_no' => '1',
                'color' => '#4D2C85',
            ],
        ];

        DB::table('settings')->where('id', 1)->update([
            'site_title_ar' => '967Sport | كرة القدم اليمنية',
            'site_title_en' => '967Sport | Yemeni Football',
            'site_desc_ar' => 'منصة 967Sport لأخبار كرة القدم اليمنية، المنتخبات، الأندية، البطولات والنتائج.',
            'site_desc_en' => '967Sport covers Yemeni football news, national teams, clubs, competitions and results.',
            'style_color1' => '#FEBB22',
            'style_color2' => '#4D2C85',
            'style_color3' => '#FFF9E9',
            'style_color4' => '#EADFF6',
            'style_logo_ar' => '967sport-facebook-logo.jpg',
            'style_logo_en' => '967sport-facebook-logo.jpg',
            'style_fav' => '967sport-facebook-logo.jpg',
            'style_apple' => '967sport-facebook-logo.jpg',
            'social_links' => json_encode($socialLinks, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);
    }
}
