<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExerciseEnrollments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exercise_enrollments', function (Blueprint $table) {
            $table->id();
			$table->foreignId('user_id')
					->nullable()
					->constrained('users')
					->onDelete('cascade' );
			$table->smallInteger('term_id')->nullable();
			$table->smallInteger('session_id')->nullable();
			$table->foreignId('exercise_id')
					->nullable()
					->constrained('exercises')
					->onDelete('cascade' );
			$table->softDeletes('deleted_at', 0);
			$table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exercise_enrollments');
    }
}
