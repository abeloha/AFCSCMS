<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyExerciseEnrollments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exercise_enrollments', function (Blueprint $table) {            
            $table->smallInteger('oral_grade')->nullable()->comment('for ds to enter grades for oral exam');
            $table->smallInteger('written_grade')->nullable()->comment('holds total grade for written exams');
            $table->smallInteger('div_grade')->nullable()->comment('marks across div');
            $table->smallInteger('dept_grade')->nullable()->comment('marks across dept');
            $table->string('love_letter')->nullable();
            $table->mediumText('log')->nullable()->comment('Logs the assigning of marks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
