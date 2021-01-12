<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();            
			$table->string('surname');
            $table->string('first_name');
            $table->string('other_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('approved')->default(false)->comment('account is approved');
			$table->boolean('confirmed')->default(false);
			$table->tinyInteger('role')->default(1)->comment('student = 1, staff=2, ds=3, ci=4, director = 5, deptycmdt=13, cmdt =14, admin=15');
            $table->boolean('is_academic')->default(false);
            $table->smallInteger('course_id')->nullable();
            $table->smallInteger('dept_id')->nullable();
            $table->smallInteger('session_id')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
