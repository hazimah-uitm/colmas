<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveSoftwareIdFromLabManagements extends Migration
{
    public function up()
    {
        Schema::table('lab_managements', function (Blueprint $table) {
            $table->dropColumn('software_id');
        });
    }
    
    public function down()
    {
        Schema::table('lab_managements', function (Blueprint $table) {
            $table->text('software_id')->nullable();  
        });
    }
    
}
