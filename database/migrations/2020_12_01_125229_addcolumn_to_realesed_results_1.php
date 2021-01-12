<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddcolumnToRealesedResults1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('released_results', function (Blueprint $table) {
            $table->bigInteger('approved_by')->after('approval')->nullable()->comment('user who approved the result');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('released_results', function (Blueprint $table) {
            //
        });
    }
}
