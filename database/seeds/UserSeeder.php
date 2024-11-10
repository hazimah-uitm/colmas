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
                'name' => 'Juhari',
                'staff_id' => '111111',
                'email' => 'john@gmail.com',
                'password' => Hash::make('user123'),
                'position_id' => 3,
                'office_phone_no' => '082111111',
                'publish_status' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Aima Sumiyati',
                'staff_id' => '222222',
                'email' => 'hiatus@gmail.com',
                'password' => Hash::make('user123'),
                'position_id' => 2,
                'office_phone_no' => '082123456',
                'publish_status' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Irene',
                'staff_id' => '555555',
                'email' => 'irene@gmail.com',
                'password' => Hash::make('user123'),
                'position_id' => 3,
                'office_phone_no' => '084123456',
                'publish_status' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Smith',
                'staff_id' => '333333',
                'email' => 'smith@gmail.com',
                'password' => Hash::make('user123'),
                'position_id' => 1,
                'office_phone_no' => '082123456',
                'publish_status' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'James',
                'staff_id' => '444444',
                'email' => 'james@gmail.com',
                'password' => Hash::make('user123'),
                'position_id' => 1,
                'office_phone_no' => '082123456',
                'publish_status' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Ken',
                'staff_id' => '666666',
                'email' => 'ken@gmail.com',
                'password' => Hash::make('user123'),
                'position_id' => 3,
                'office_phone_no' => '082123456',
                'publish_status' => true,
                'email_verified_at' => now(),
            ],
        ]);

        // associate the users with campuses in the pivot table (campus_user)
        $userData = [
            ['user_id' => 2, 'campus_id' => 1],
            ['user_id' => 3, 'campus_id' => 2],
            ['user_id' => 4, 'campus_id' => 3],
            ['user_id' => 5, 'campus_id' => 1],
            ['user_id' => 6, 'campus_id' => 2],
            ['user_id' => 7, 'campus_id' => 3],
        ];

        // Insert associations into the campus_user pivot table
        DB::table('campus_user')->insert($userData);
    }
}
