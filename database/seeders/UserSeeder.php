<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert(
            [
                [
                    'acc_type_id'         => 5,
                    'age_type_id'         => 1,
                    'ethnicity_id'        => 1,
                    'name'                => 'admin',
                    'email'               => 'admin@test.com',
                    'password'            => '$2a$12$0A1ghgYglhzUdUbFXoQrM.kaZnGqeJ1qRl8vp/ivRmStLdokGDLhW',
                    'avatar'              => "sample_avatars/1.png",
                ],
                [
                    'acc_type_id'         => 2,
                    'age_type_id'         => 1,
                    'ethnicity_id'        => 1,
                    'name'                => 'developer',
                    'email'               => 'dev@test.com',
                    'password'            => '$2a$12$0A1ghgYglhzUdUbFXoQrM.kaZnGqeJ1qRl8vp/ivRmStLdokGDLhW',
                    'avatar'              => "sample_avatars/1.png",
                ],
            ]
        );
    }
}
