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
                'title' => 'Adobe Animate CC',
                'publish_status' => true
            ],
            [
                'title' => 'Adobe Photoshop CC',
                'publish_status' => true
            ],
        ]);
    }
}
