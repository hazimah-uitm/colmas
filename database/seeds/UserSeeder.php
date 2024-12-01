<?php

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::insert([
            [
                'name' => 'Sam',
                'staff_id' => '333333',
                'email' => 'sam@gmail.com',
                'password' => Hash::make('user123'),
                'position_id' => 3,
                'office_phone_no' => '082111111',
                'publish_status' => true,
                'email_verified_at' => now(),
            ],
        ]);

        // associate the users with campuses in the pivot table (campus_user)
        $userData = [
            ['user_id' => 23, 'campus_id' => 2],
        ];

        // Insert associations into the campus_user pivot table
        DB::table('campus_user')->insert($userData);
    }
}
