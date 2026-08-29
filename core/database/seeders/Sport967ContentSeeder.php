<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Sport967ContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            Sport967PlatformSeeder::class,
            Sport967NewsSeeder::class,
            Sport967SliderSeeder::class,
            Sport967MenusSeeder::class,
        ]);
    }
}
