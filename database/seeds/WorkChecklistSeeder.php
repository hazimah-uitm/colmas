<?php

use App\Models\WorkChecklist;
use Illuminate\Database\Seeder;

class WorkChecklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        WorkChecklist::insert([
            [
                'title' => 'Disk Cleanup',
                'publish_status' => true,
            ],
            [
                'title' => 'Scandisk',
                'publish_status' => true,
            ],
            [
                'title' => 'Antivirus',
                'publish_status' => true,
            ],
            [
                'title' => 'Windows Update',
                'publish_status' => true,
            ],
            [
                'title' => 'Disk Defragmenter',
                'publish_status' => true,
            ],
            [
                'title' => 'Rangkaian',
                'publish_status' => true,
            ],
            [
                'title' => 'Ghosting',
                'publish_status' => false,
            ],
        ]);
    }
}
