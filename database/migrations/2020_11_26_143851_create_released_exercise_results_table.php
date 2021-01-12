<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReleasedExerciseResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('released_exercise_results', function (Blueprint $table) {            
            
            $table->id();

            $table->smallInteger('session_id');			
            $table->smallInteger('term_id');

            $table->foreignId('exercise_id')
                ->nullable()
                ->constrained('exercises')
                ->onDelete('set null' );

            $table->foreignId('user_id')
                ->nullable()
                ->comment('user who approved the exercise result');

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
        Schema::dropIfExists('released_exercise_results');
    }
}
