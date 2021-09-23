<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSysUserFbtokenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sys_user_fbtoken', function (Blueprint $table) {
            $table->foreign('fbtoken_user_id', 'fbtoken_user_id')->references('user_id')->on('sys_user')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sys_user_fbtoken', function (Blueprint $table) {
            $table->dropForeign('fbtoken_user_id');
        });
    }
}
