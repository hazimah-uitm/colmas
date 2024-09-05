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
                'code' => 'B4003',
                'name' => 'Makmal Komputer 1',
                'campus_id' => 2,
                'pemilik_id' => 2,
                'username' => 'UiTM',
                'password' => 'uitm123',
                'no_of_computer' => 15,
                'publish_status' => true
            ],
            [
                'code' => 'B4002',
                'name' => 'Makmal Komputer 2',
                'campus_id' => 2,
                'pemilik_id' => 2,
                'username' => 'UiTM 1',
                'password' => 'uitm123',
                'no_of_computer' => 30,
                'publish_status' => true
            ],
        ]);
    }
}
