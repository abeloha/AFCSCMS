<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResultAddedTermWpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('result_added_term_wps', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('user_id')
                ->nullable()
                ->comment('student with the result');

            $table->decimal('wp', 8,2)->default(0.00)->nullable();

            $table->smallInteger('session_id');			
            $table->smallInteger('term_id');
            
            $table->foreignId('added_by_user_id')
                ->nullable()
                ->comment('user who approved the result');
            
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
        Schema::dropIfExists('result_added_term_wp');
    }
}
