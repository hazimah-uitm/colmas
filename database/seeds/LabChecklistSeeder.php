<?php

use App\Models\LabChecklist;
use Illuminate\Database\Seeder;

class LabChecklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LabChecklist::insert([
            [
                'title' => 'PC dinomborkan',
                'publish_status' => true
            ],
            [
                'title' => 'Pintu Kayu',
                'publish_status' => true
            ],
            [
                'title' => 'Pintu Grill',
                'publish_status' => true
            ],
            [
                'title' => 'Penghawa Dingin',
                'publish_status' => true
            ],
            [
                'title' => 'Peraturan Makmal',
                'publish_status' => true
            ],
            [
                'title' => 'Tanda Nama Makmal',
                'publish_status' => true
            ],
            [
                'title' => 'Whiteboard',
                'publish_status' => true
            ]
        ]);
    }
}
