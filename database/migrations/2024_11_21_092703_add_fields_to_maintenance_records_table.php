<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToMaintenanceRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('maintenance_records', function (Blueprint $table) {
            $table->string('keluar_location')->nullable();
            $table->string('keluar_officer')->nullable();
            $table->date('keluar_date')->nullable();
            $table->date('kembali_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('maintenance_records', function (Blueprint $table) {
            $table->dropColumn('keluar_location');
            $table->dropColumn('keluar_officer');
            $table->dropColumn('keludropColumn');
            $table->dropColumn('kembali_date');
        });
    }
}
