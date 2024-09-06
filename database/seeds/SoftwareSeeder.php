<?php

use App\Models\Software;
use Illuminate\Database\Seeder;

class SoftwareSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Software::insert([
            [
                'title' => 'Adobe Photoshop',
                'publish_status' => true
            ],
            [
                'title' => 'AutoCAD',
                'publish_status' => true
            ],
        ]);
    }
}
