<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ExerciseEnrollmentDropColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exercise_enrollments', function (Blueprint $table) {
            $table->dropColumn('oral_grade');
            $table->dropColumn('written_grade');
            $table->dropColumn('div_grade');
            $table->dropColumn('dept_grade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exercise_enrollments', function (Blueprint $table) {
            //
        });
    }
}
