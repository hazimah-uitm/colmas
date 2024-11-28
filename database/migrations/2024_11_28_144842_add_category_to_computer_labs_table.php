<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCategoryToComputerLabsTable extends Migration
{
    public function up()
    {
        Schema::table('computer_labs', function (Blueprint $table) {
            $table->enum('category', ['makmal_komputer', 'sudut_it', 'pusat_data'])->nullable()->after('location');
        });
    }
    
    public function down()
    {
        Schema::table('computer_labs', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
}
