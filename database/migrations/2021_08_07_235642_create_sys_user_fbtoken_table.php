<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->engine = 'InnoDB';
            $table->bigIncrements('fbtoken_id');
            $table->unsignedBigInteger('fbtoken_user_id')->nullable()->default(null);
            $table->string('fbtoken_token')->nullable()->default(null);
            $table->string('fbtoken_agent')->nullable()->default(null);
            $table->string('fbtoken_ip')->nullable()->default(null);
            $table->timestamp('fbtoken_created_at')->nullable()->useCurrent();
            $table->timestamp('fbtoken_updated_at')->nullable();

            $table->index(["fbtoken_user_id"], 'fbtoken_user_id');

            $table->foreign('fbtoken_user_id', 'fbtoken_user_id')
                ->references('user_id')->on('sys_user')
                ->onDelete('cascade')
                ->onUpdate('cascade');
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
