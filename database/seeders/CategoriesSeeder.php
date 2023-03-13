<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert(
            [
                [
                    'name' => 'Free Courses',
                ],
                [
                    'name' => 'Kids',
                ],
                [
                    'name' => 'Audio',
                ],
                [
                    'name' => 'Language',
                ],
                [
                    'name' => 'Culture & History',
                ]
            ]
        );
    }
}
