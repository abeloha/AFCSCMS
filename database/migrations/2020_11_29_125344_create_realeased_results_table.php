<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRealeasedResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('released_results', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('div_id')->nullable();
            $table->bigInteger('dept_id')->nullable();
            $table->boolean('approval')->default(0);
            $table->smallInteger('course_id');		
            $table->smallInteger('session_id');			
            $table->smallInteger('term_id');
            $table->foreignId('user_id')
                ->nullable()
                ->comment('user who creates release record');
                
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
        Schema::dropIfExists('realeased_results');
    }
}
