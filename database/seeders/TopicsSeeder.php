<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class TopicsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('topics')->insert(
            [
                [
                    'name'      => 'Language',
                    'image'     => 'topics/language.png',
                    'is_active' => 1
                ],
                [
                    'name'      => 'Culture / History',
                    'image'     => 'topics/culture.png',
                    'is_active' => 1
                ],
                [
                    'name'      => 'Art / Craft',
                    'image'     => 'topics/art.png',
                    'is_active' => 1
                ],
                [
                    'name'      => 'Music / Audio',
                    'image'     => 'topics/music.png',
                    'is_active' => 1
                ],
                [
                    'name'      => 'Sports / Dance',
                    'image'     => 'topics/sport.png',
                    'is_active' => 1
                ],
                [
                    'name'      => 'Science / Technology',
                    'image'     => 'topics/science.png',
                    'is_active' => 1
                ],
                [
                    'name'      => 'Documentary',
                    'image'     => 'topics/docmentary.png',
                    'is_active' => 1
                ],
                [
                    'name'      => 'Entertainment',
                    'image'     => 'topics/entertainment.png',
                    'is_active' => 1
                ],
                [
                    'name'      => 'Lifestyle',
                    'image'     => 'topics/lifestyle.png',
                    'is_active' => 1
                ],
                [
                    'name'      => 'Free Courses',
                    'image'     => 'topics/language.png',
                    'is_active' => 1
                ],
                [
                    'name'      => 'Kids',
                    'image'     => 'topics/language.png',
                    'is_active' => 1
                ],
                [
                    'name'      => 'Audio',
                    'image'     => 'topics/language.png',
                    'is_active' => 1
                ]
            ]
        );
    }
}
