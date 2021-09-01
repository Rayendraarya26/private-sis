<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSysUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_user', function (Blueprint $table) {
            $table->bigIncrements('user_id');
            $table->string('user_email')->unique('user_email');
            $table->string('user_fullname')->nullable();
            $table->string('user_password')->nullable();
            $table->string('user_token')->nullable();
            $table->enum('user_is_active', ['yes', 'no'])->default('yes');
            $table->enum('user_is_banned', ['yes', 'no'])->default('no');
            $table->string('user_picture')->nullable()->default('/images/profiles/default.png');
            $table->timestamp('user_last_login')->nullable();
            $table->timestamp('user_active_at')->nullable();
            $table->timestamp('user_banned_at')->nullable();
            $table->timestamp('user_created_at')->nullable()->useCurrent();
            $table->timestamp('user_updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_user');
    }
}
