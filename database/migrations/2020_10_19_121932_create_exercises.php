<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExercises extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->string('name');
			$table->string('description');
			$table->smallInteger('dept_id');
			$table->smallInteger('course_id');			
            $table->smallInteger('term_id')->nullable();
			$table->foreignId('sponsor_user_id')
					->nullable()
					->constrained('users')
					->onDelete('set null' );
			$table->foreignId('cosponsor_user_id')
					->nullable()
					->constrained('users')
					->onDelete('set null' );			
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
        Schema::dropIfExists('exercises');
    }
}
