<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequirementSubmissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requirement_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id'); 
            $table->bigInteger('requirement_id');

            $table->longText('submitted_text')->nullable();
            $table->string('submitted_file')->nullable();
            
            $table->decimal('grade', 8, 2)->nullable();
            $table->string('correction_file')->nullable();
            $table->bigInteger('grader_id')
                ->nullable()
                ->comment('the staff who graded'); 

            $table->dateTime('graded_at')->nullable();

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
        Schema::dropIfExists('requirement_submissions');
    }
}
