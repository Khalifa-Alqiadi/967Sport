<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(LanguagesSeeder::class);
        $this->call(PermissionsSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(WebmasterSettingsSeeder::class);
        $this->call(WebmasterBannersSeeder::class);
        $this->call(WebmasterSectionsSeeder::class);
        $this->call(SettingsSeeder::class);
        $this->call(CountriesSeeder::class);
        $this->call(ContactsGroupsSeeder::class);
        $this->call(MenusSeeder::class);
        $this->call(BannersSeeder::class);
        $this->call(PopupsSeeder::class);
        $this->call(CategoriesSeeder::class);
        $this->call(TopicsSeeder::class);
        $this->call(WebmailGroupsSeeder::class);
        $this->call(MiddleEastUnionContentSeeder::class);
        $this->call(Rebrand967SportSeeder::class);
        $this->call(Sport967NavigationBannersSeeder::class);
        $this->call(Sport967ContentSeeder::class);
        $this->call(YemeniLeague2023_2024Seeder::class);
        $this->call(YemeniCurrentCompetitionsSeeder::class);
        $this->call(YemeniCurrentPlayersSeeder::class);
    }
}
