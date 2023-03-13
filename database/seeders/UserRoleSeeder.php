<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_roles')->insert(
            [
                [
                    'name'        => 'Educator',
                    'description' => 'I want to teach',
                    'is_active'   => 1
                ],
                [
                    'name'        => 'Learner',
                    'description' => 'I want to learn',
                    'is_active'   => 1
                ],
                [
                    'name'        => 'Admin',
                    'description' => 'Administrator',
                    'is_active'   => 1
                ]
            ]
        );
    }
}
