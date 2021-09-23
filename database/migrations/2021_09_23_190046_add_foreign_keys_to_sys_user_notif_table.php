<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSysUserNotifTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sys_user_notif', function (Blueprint $table) {
            $table->foreign('notif_user_id', 'notif_user_id')->references('user_id')->on('sys_user')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sys_user_notif', function (Blueprint $table) {
            $table->dropForeign('notif_user_id');
        });
    }
}
