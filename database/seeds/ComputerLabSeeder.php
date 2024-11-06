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
                'pemilik_id' => 3,
                'username' => 'UiTM',
                'password' => 'uitm123',
                'no_of_computer' => 5,
                'publish_status' => true
            ],
            [
                'code' => 'B4002',
                'name' => 'Makmal Komputer 2',
                'campus_id' => 2,
                'pemilik_id' => 2,
                'username' => 'UiTM 1',
                'password' => 'uitm123',
                'no_of_computer' => 3,
                'publish_status' => true
            ],
            [
                'code' => 'B4003',
                'name' => 'Makmal Komputer 3',
                'campus_id' => 2,
                'pemilik_id' => 2,
                'username' => 'UiTM 1',
                'password' => 'uitm123',
                'no_of_computer' => 2,
                'publish_status' => true
            ],
        ]);
    }
}
