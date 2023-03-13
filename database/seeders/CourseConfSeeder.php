<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseConfSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('course_configurations')->insert([
            [
                'title'       => 'course_fee',
                'value'       => 4,
                'description' => 'This is the course fee in percent',
                'created_at'  => now()
            ]
        ]);
    }
}
