<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DivsAddTc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('divs', function (Blueprint $table) {
            $table->foreignId('tc_user_id')
                    ->after('ci_user_id')
					->nullable()
					->constrained('users')
					->onDelete('set null' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('divs', function (Blueprint $table) {
            //
        });
    }
}
