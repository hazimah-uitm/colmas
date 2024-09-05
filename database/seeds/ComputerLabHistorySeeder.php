<?php

use App\Models\ComputerLab;
use App\Models\ComputerLabHistory;
use Illuminate\Database\Seeder;

class ComputerLabHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $labs = ComputerLab::all();

        foreach ($labs as $lab) {
            ComputerLabHistory::create([
                'computer_lab_id' => $lab->id,
                'code' => $lab->code,
                'name' => $lab->name,
                'pc_no' => $lab->no_of_computer,
                'owner' => $lab->pemilik_id,
                'month_year' => now(),
                'action' => 'Tambah',
                'publish_status' => 1,
            ]);
        }
    }
}
