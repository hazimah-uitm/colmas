<?php

use App\Models\ComputerLab;
use Illuminate\Database\Seeder;

class ComputerLabSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ComputerLab::insert([
            [
                'code' => 'B4001',
                'name' => 'Makmal Komputer 1',
                'campus_id' => 1,
                'pemilik_id' => 2,
                'username' => 'UiTM',
                'password' => 'uitm123',
                'no_of_computer' => 2,
                'publish_status' => true
            ]
        ]);
    }
}
