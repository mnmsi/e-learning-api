<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        $this->call([
            SampleAvatarSeeder::class,
            CategoriesSeeder::class,
            TopicsSeeder::class,
            UserRoleSeeder::class,
            UserAccTypeSeeder::class,
            UserAgeTypesSeeder::class,
            UserEthnicitySeeder::class,
            UserSeeder::class,
            CourseTagSeeder::class,
            CourseConfSeeder::class
        ]);
    }
}
