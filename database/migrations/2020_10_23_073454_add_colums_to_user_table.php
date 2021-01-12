<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumsToUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('picture')->nullable();
            $table->string('rank')->nullable();
            $table->string('svc_no')->nullable();
			$table->string('service')->nullable();
			$table->string('corps')->nullable();
            $table->string('branch')->nullable();
            $table->string('specialty')->nullable();
            $table->string('commission')->nullable();
            $table->string('sex')->nullable();
            $table->string('country')->nullable();
			$table->string('account')->nullable();
            $table->string('bank')->nullable();
            $table->string('last_unit_1')->nullable();
            $table->string('last_appointment_1')->nullable();
            $table->string('last_unit_2')->nullable();
            $table->string('last_appointment_2')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
