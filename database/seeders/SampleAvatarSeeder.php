<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class SampleAvatarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sample_avatars')->insert(
            [
                [
                    'image' => 'sample_avatars/1.png',
                ],
                [
                    'image' => 'sample_avatars/2.png',
                ],
                [
                    'image' => 'sample_avatars/3.png',
                ],
                [
                    'image' => 'sample_avatars/4.png',
                ],
                [
                    'image' => 'sample_avatars/5.png',
                ],
                [
                    'image' => 'sample_avatars/6.png',
                ],
                [
                    'image' => 'sample_avatars/7.png',
                ],
                [
                    'image' => 'sample_avatars/8.png',
                ]
            ]
        );
    }
}
