<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSysGroupPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sys_group_permission', function (Blueprint $table) {
            $table->foreign('action_id', 'action_id')->references('action_id')->on('sys_menu_action')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('group_id', 'group_id')->references('group_id')->on('sys_group')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sys_group_permission', function (Blueprint $table) {
            $table->dropForeign('action_id');
            $table->dropForeign('group_id');
        });
    }
}
