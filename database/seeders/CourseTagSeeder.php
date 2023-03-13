<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('course_tags')->insert([
            [
                'title'       => 'hot_pick',
                'value'       => 4,
                'description' => 'Add "HOT PICK" tag in course when total enrolled learner is grater then or equal this value',
                'created_at'  => now()
            ]
        ]);
    }
}
