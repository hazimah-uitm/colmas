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
                'title' => 'Adobe Animate CC 2023',
                'publish_status' => true
            ],
            [
                'title' => 'Adobe Photoshop CC 2023',
                'publish_status' => true
            ],
        ]);
    }
}
