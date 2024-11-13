<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveSoftwareIdFromComputerLabs extends Migration
{
    public function up()
    {
        Schema::table('computer_labs', function (Blueprint $table) {
            $table->dropColumn('software_id');
        });
    }
    
    public function down()
    {
        Schema::table('computer_labs', function (Blueprint $table) {
            $table->text('software_id')->nullable();  
        });
    }
}
