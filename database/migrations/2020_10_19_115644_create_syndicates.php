<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSyndicates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('syndicates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->smallInteger('div_id');
			$table->foreignId('ds_user_id')
					->nullable()
					->constrained('users')
					->onDelete('set null' );
			$table->smallInteger('session_id')->nullable();
			$table->softDeletes('deleted_at', 0);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('syndicates');
    }
}
