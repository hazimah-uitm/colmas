<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComputerLabHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('computer_lab_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('computer_lab_id');
            $table->string('code')->nullable();
            $table->string('name');
            $table->string('pc_no');
            $table->unsignedBigInteger('owner');
            $table->integer('publish_status');
            $table->timestamp('month_year');
            $table->string('action'); 
            $table->timestamps();

            $table->foreign('computer_lab_id')->references('id')->on('computer_labs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('computer_lab_histories');
    }
}
