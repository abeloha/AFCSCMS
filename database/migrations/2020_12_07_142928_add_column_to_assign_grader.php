<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToAssignGrader extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('grading_assignments', function (Blueprint $table) {
            $table->smallInteger('session_id')->after('assigned_user_id')->nullable();			
            $table->smallInteger('term_id')->after('session_id')->nullable();;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('grading_assignments', function (Blueprint $table) {
            //
        });
    }
}
