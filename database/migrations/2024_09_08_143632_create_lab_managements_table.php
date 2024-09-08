<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateLabManagementsTable extends Migration
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

        Schema::create('lab_managements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('computer_lab_id');
            $table->text('lab_checklist_id')->nullable();
            $table->text('software_id')->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->integer('computer_no');
            $table->integer('pc_maintenance_no')->nullable();
            $table->integer('pc_unmaintenance_no')->nullable();
            $table->integer('pc_damage_no')->nullable();
            $table->text('remarks_submitter')->nullable();
            $table->text('remarks_checker')->nullable();
            $table->string('status')->default('draft');
            $table->unsignedBigInteger('checked_by')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->unsignedBigInteger('submitted_by')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['computer_lab_id', 'start_time', 'end_time']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lab_managements');
    }
}
