<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ExerciseEnrollmentRecreateColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exercise_enrollments', function (Blueprint $table) {
            $table->decimal('oral_grade', 8,2)->nullable()->comment('for ds to enter grades for oral exam');
            $table->decimal('written_grade', 8,2)->nullable()->comment('holds total grade for written exams');
            $table->decimal('ci_wp_grade', 8,2)->nullable()->comment('marks added after wighted point');
            $table->decimal('dpty_cmd_wp_grade', 8,2)->nullable()->comment('marks added after dept');
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
