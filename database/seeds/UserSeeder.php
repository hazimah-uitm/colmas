<?php

use App\Models\User;
use Illuminate\Database\Seeder;
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
                'name' => 'John Doe',
                'staff_id' => '111111',
                'email' => 'john@gmail.com',
                'password' => Hash::make('user123'),
                'position_id' => 3,
                'campus_id' => 1,
                'office_phone_no' => '082111111',
                'publish_status' => true,
                'email_verified_at' => now(), 
            ]
        ]);
    }
}
