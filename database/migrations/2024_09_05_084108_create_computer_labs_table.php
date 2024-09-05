<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComputerLabsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('computer_labs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->nullable();
            $table->string('name');
            $table->unsignedBigInteger('campus_id');
            $table->foreign('campus_id')->references('id')->on('campuses')->onDelete('CASCADE');
            $table->unsignedBigInteger('pemilik_id');
            $table->foreign('pemilik_id')->references('id')->on('users')->onDelete('CASCADE');
            $table->integer('no_of_computer');
            $table->string('username');
            $table->string('password');
            $table->boolean('publish_status');
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['name', 'campus_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('computer_labs');
    }
}
