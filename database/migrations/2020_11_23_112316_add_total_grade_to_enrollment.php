<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalGradeToEnrollment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exercise_enrollments', function (Blueprint $table) {
            $table->decimal('total_grade', 8,2)->after('written_grade')->nullable()->comment('Total grade before weighted point');
            $table->decimal('wp_grade', 8,2)->after('total_grade')->nullable()->comment('weighted point value. Calculated when ds approves result');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exercise_enrollments', function (Blueprint $table) {
            //
        });
    }
}
