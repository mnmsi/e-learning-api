<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class UserAgeTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_age_types')->insert(
            [
                [
                    'name' => 'Below 16',
                    'is_active' => 1
                ],
                [
                    'name' => '16 - 25',
                    'is_active' => 1
                ],
                [
                    'name' => '26 - 35',
                    'is_active' => 1
                ],
                [
                    'name' => '36 - 45',
                    'is_active' => 1
                ]
            ]
        );
    }
}
