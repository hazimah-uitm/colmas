<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateMaintenanceRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $jsonType = DB::getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql' &&
                    version_compare(DB::getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION), '10.2.7', '>=')
                    ? 'json' : 'text';

        Schema::create('maintenance_records', function (Blueprint $table) use ($jsonType) {
            $table->bigIncrements('id');
            $table->string('computer_name')->nullable();
            $table->unsignedBigInteger('lab_management_id');
            $table->string('ip_address')->nullable();
            $table->text('work_checklist_id')->nullable();
            $table->string('aduan_unit_no')->nullable();
            $table->string('vms_no')->nullable();
            $table->text('remarks')->nullable();
            $table->string('entry_option');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('lab_management_id')->references('id')->on('lab_managements')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maintenance_records');
    }
}
