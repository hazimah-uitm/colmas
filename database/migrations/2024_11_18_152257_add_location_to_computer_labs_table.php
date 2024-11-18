<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLocationToComputerLabsTable extends Migration
{
    public function up()
    {
        Schema::table('computer_labs', function (Blueprint $table) {
            $table->string('location')->nullable(); // Adding nullable location column
        });
    }
    
    public function down()
    {
        Schema::table('computer_labs', function (Blueprint $table) {
            $table->dropColumn('location'); // Dropping the column in case of rollback
        });
    }
    
}
