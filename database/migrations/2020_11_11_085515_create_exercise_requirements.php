<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExerciseRequirements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exercise_requirements', function (Blueprint $table) {
            
            $table->id();
            
			$table->foreignId('exercise_id')
                ->nullable();            
            
            $table->string('title')->nullable();
            $table->decimal('marks', 8, 2)->default(0);
            $table->tinyInteger('req_type')->default(1)->comment('1 = written, 2 = utw (oral)');
            $table->tinyInteger('submission_type')->nullable()->comment('Null = not set, 1 = file, 2 = text');

            $table->text('question')->nullable();
            $table->string('question_file')->nullable();

            $table->text('grading_instruction')->nullable()->comment('for ds doing the grading');
            $table->string('grading_file_1')->nullable()->comment('for ds doing the grading');
            $table->string('grading_file_2')->nullable()->comment('for ds doing the grading');

            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->dateTime('show_at')->nullable();

			$table->smallInteger('term_id')->nullable();
			$table->smallInteger('session_id')->nullable();
            $table->foreignId('user_id')
                ->nullable()
                ->comment('the staff who created the record');

            $table->softDeletes('deleted_at', 0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exercise_requirements');
    }
}
