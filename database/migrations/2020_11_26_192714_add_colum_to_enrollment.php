<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumToEnrollment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exercise_enrollments', function (Blueprint $table) {            
            $table->decimal('total_wp', 8,2)->after('dpty_cmd_wp_grade')->nullable()->comment('weighted point overall of final result');        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exercise_enrollmrnts', function (Blueprint $table) {
            //
        });
    }
}
