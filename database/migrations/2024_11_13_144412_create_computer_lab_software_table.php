<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComputerLabSoftwareTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('computer_lab_software', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('computer_lab_id'); 
            $table->unsignedBigInteger('software_id'); 
            $table->timestamps();

            $table->foreign('computer_lab_id')->references('id')->on('computer_labs')->onDelete('cascade');
            $table->foreign('software_id')->references('id')->on('software')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('computer_lab_software');
    }
}
