<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequirementGrades extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requirement_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id'); 
            $table->bigInteger('requirement_id');
            $table->decimal('grade', 8, 2)->nullable();
            $table->bigInteger('grader_id')
                ->nullable()
                ->comment('the staff who graded');            
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
        Schema::dropIfExists('requirement_grades');
    }
}
