<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImageToComputerLabsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('computer_labs', function (Blueprint $table) {
            $table->string('jadual_kuliah')->nullable();  // Add image column
        });
    }
    
    public function down()
    {
        Schema::table('computer_labs', function (Blueprint $table) {
            $table->dropColumn('jadual_kuliah');  // Drop image column
        });
    }
}
