<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateComputerLabsTable extends Migration
{
    public function up()
    {
        Schema::table('computer_labs', function (Blueprint $table) {
            // Drop the username and password columns
            $table->dropColumn(['username', 'password']);
            
            // Add the new user_credentials text column
            $table->text('user_credentials')->nullable();
        });
    }

    public function down()
    {
        Schema::table('computer_labs', function (Blueprint $table) {
            // Add back the username and password columns
            $table->string('username');
            $table->string('password');
            
            // Drop the user_credentials column
            $table->dropColumn('user_credentials');
        });
    }
}
