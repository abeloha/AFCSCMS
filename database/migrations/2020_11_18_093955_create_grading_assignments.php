<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGradingAssignments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grading_assignments', function (Blueprint $table) {
            
            $table->id();
            
            $table->bigInteger('user_id')->nullable();
            
            $table->foreignId('exercise_id')
					->nullable()
					->constrained('exercises')
                    ->onDelete('cascade' );
                   
            $table->bigInteger('assigned_user_id')->nullable();

            $table->softDeletes();
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
        Schema::dropIfExists('grading_assignments');
    }
}
