<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSyndicateEnrollments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('syndicate_enrollments', function (Blueprint $table) {
            $table->id();
			$table->foreignId('user_id')
					->nullable()
					->constrained('users')
					->onDelete('cascade' );
			$table->smallInteger('term_id')->nullable();
			$table->smallInteger('session_id')->nullable();
			$table->foreignId('syndicate_id')
					->nullable()
					->constrained('syndicates')
					->onDelete('cascade' );
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
        Schema::dropIfExists('syndicate_enrollments');
    }
}
