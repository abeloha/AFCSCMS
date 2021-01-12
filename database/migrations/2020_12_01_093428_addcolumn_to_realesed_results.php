<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddcolumnToRealesedResults extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('released_results', function (Blueprint $table) {
            $table->string('ci_comment')->nullable();
            $table->string('director_comment')->nullable();
            $table->string('depty_cmd_comment')->nullable();
            $table->string('cmd_comment')->nullable();
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
