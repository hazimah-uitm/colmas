<?php

use App\Models\ComputerLab;
use App\Models\LabChecklist;
use App\Models\LabManagement;
use App\Models\Software;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class LabManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Assuming you have some computer labs, lab checklists, and software records in your database
        $computerLabs = ComputerLab::pluck('id')->toArray();
        $labChecklists = LabChecklist::pluck('id')->toArray();
        $softwareList = Software::pluck('id')->toArray();

        foreach (range(1, 10) as $index) {
            $labChecklistCount = $faker->numberBetween(1, 4);
            $softwareCount = $faker->numberBetween(1, 4);

            LabManagement::create([
                'computer_lab_id' => $faker->randomElement($computerLabs),
                'lab_checklist_id' => $faker->randomElements($labChecklists, $labChecklistCount),
                'software_id' => $faker->randomElements($softwareList, $softwareCount),
                'start_time' => $faker->dateTime,
                'end_time' => $faker->dateTime,
                'computer_no' => $faker->numberBetween(1, 20),
                'unusable_computer_no' => $faker->numberBetween(0, 5),
                'usable_computer_no' => $faker->numberBetween(15, 20),
                'remarks' => $faker->text,
                'status' => "draft",
            ]);
        }
    }
}
