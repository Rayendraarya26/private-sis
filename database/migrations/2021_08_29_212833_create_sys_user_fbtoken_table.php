<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSysUserFbtokenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_user_fbtoken', function (Blueprint $table) {
            $table->bigIncrements('fbtoken_id');
            $table->unsignedBigInteger('fbtoken_user_id')->nullable()->index('fbtoken_user_id');
            $table->string('fbtoken_token')->nullable();
            $table->string('fbtoken_agent')->nullable();
            $table->string('fbtoken_ip')->nullable();
            $table->timestamp('fbtoken_created_at')->nullable()->useCurrent();
            $table->timestamp('fbtoken_updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_user_fbtoken');
    }
}
