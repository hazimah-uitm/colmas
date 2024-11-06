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
                'code' => 'MKOM1',
                'name' => 'Makmal Komputer 1',
                'campus_id' => 1,
                'pemilik_id' => 2,
                'username' => 'UiTM',
                'password' => 'uitm123',
                'no_of_computer' => 10,
                'publish_status' => true
            ],
            [
                'code' => 'MKOM2',
                'name' => 'Makmal Komputer 2',
                'campus_id' => 1,
                'pemilik_id' => 2,
                'username' => 'UiTM',
                'password' => 'uitm123',
                'no_of_computer' => 10,
                'publish_status' => true
            ],
            [
                'code' => 'MKOM3',
                'name' => 'Makmal Komputer 3',
                'campus_id' => 1,
                'pemilik_id' => 2,
                'username' => 'UiTM',
                'password' => 'uitm123',
                'no_of_computer' => 10,
                'publish_status' => true
            ],
            [
                'code' => 'MKOM4',
                'name' => 'Makmal Komputer 4',
                'campus_id' => 1,
                'pemilik_id' => 2,
                'username' => 'UiTM',
                'password' => 'uitm123',
                'no_of_computer' => 10,
                'publish_status' => true
            ],
            [
                'code' => 'MKOM5',
                'name' => 'Makmal Komputer 5',
                'campus_id' => 1,
                'pemilik_id' => 2,
                'username' => 'UiTM',
                'password' => 'uitm123',
                'no_of_computer' => 10,
                'publish_status' => true
            ],
            [
                'code' => 'MKOM6',
                'name' => 'Makmal Komputer 6',
                'campus_id' => 1,
                'pemilik_id' => 2,
                'username' => 'UiTM',
                'password' => 'uitm123',
                'no_of_computer' => 10,
                'publish_status' => true
            ],
            [
                'code' => 'MKOM7',
                'name' => 'Makmal Komputer 7',
                'campus_id' => 1,
                'pemilik_id' => 2,
                'username' => 'UiTM',
                'password' => 'uitm123',
                'no_of_computer' => 10,
                'publish_status' => true
            ],
            [
                'code' => 'MKOM8',
                'name' => 'Makmal Komputer 8',
                'campus_id' => 1,
                'pemilik_id' => 2,
                'username' => 'UiTM',
                'password' => 'uitm123',
                'no_of_computer' => 10,
                'publish_status' => true
            ],
            [
                'code' => 'MKOM9',
                'name' => 'Makmal Komputer 9',
                'campus_id' => 1,
                'pemilik_id' => 2,
                'username' => 'UiTM',
                'password' => 'uitm123',
                'no_of_computer' => 10,
                'publish_status' => true
            ],
            [
                'code' => 'MKOM10',
                'name' => 'Makmal Komputer 10',
                'campus_id' => 1,
                'pemilik_id' => 2,
                'username' => 'UiTM',
                'password' => 'uitm123',
                'no_of_computer' => 10,
                'publish_status' => true
            ],
            [
                'code' => 'B4001',
                'name' => 'B4001',
                'campus_id' => 2,
                'pemilik_id' => 3,
                'username' => 'UiTM 1',
                'password' => 'uitm123',
                'no_of_computer' => 3,
                'publish_status' => true
            ],
            [
                'code' => 'B4002',
                'name' => 'B4002',
                'campus_id' => 2,
                'pemilik_id' => 3,
                'username' => 'UiTM 1',
                'password' => 'uitm123',
                'no_of_computer' => 10,
                'publish_status' => true
            ],
            [
                'code' => 'B4003',
                'name' => 'B4003',
                'campus_id' => 2,
                'pemilik_id' => 3,
                'username' => 'UiTM 1',
                'password' => 'uitm123',
                'no_of_computer' => 10,
                'publish_status' => true
            ],
            [
                'code' => 'B4004',
                'name' => 'B4004',
                'campus_id' => 2,
                'pemilik_id' => 3,
                'username' => 'UiTM 1',
                'password' => 'uitm123',
                'no_of_computer' => 6,
                'publish_status' => true
            ],
            [
                'code' => 'B4005',
                'name' => 'B4005',
                'campus_id' => 2,
                'pemilik_id' => 3,
                'username' => 'UiTM 1',
                'password' => 'uitm123',
                'no_of_computer' => 6,
                'publish_status' => true
            ],
            [
                'code' => 'CL001',
                'name' => 'CL001',
                'campus_id' => 3,
                'pemilik_id' => 4,
                'username' => 'UiTM 1',
                'password' => 'uitm123',
                'no_of_computer' => 2,
                'publish_status' => true
            ],
        ]);
    }
}
