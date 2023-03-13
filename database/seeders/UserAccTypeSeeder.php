<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class UserAccTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_acc_types')->insert(
            [
                [
                    'role_id' => 1,
                    'name' => 'Teacher/Educator',
                    'description' => 'Teacher/Educator',
                    'is_active' => 1
                ],
                [
                    'role_id' => 1,
                    'name' => 'Organisation/School',
                    'description' => 'Organisation/School',
                    'is_active' => 1
                ],
                [
                    'role_id' => 2,
                    'name' => 'Personal Learner',
                    'description' => 'Personal Learner',
                    'is_active' => 1
                ],
                [
                    'role_id' => 2,
                    'name' => 'Parent/Family Learner',
                    'description' => 'Parent/Family Learner',
                    'is_active' => 1
                ],
                [
                    'role_id' => 3,
                    'name' => 'Admin',
                    'description' => 'Admin',
                    'is_active' => 1
                ],
            ]
        );
    }
}
