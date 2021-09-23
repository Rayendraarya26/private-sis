<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSysUserGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sys_user_group', function (Blueprint $table) {
            $table->foreign('ug_group_id', 'ug_group_id')->references('group_id')->on('sys_group')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('ug_user_id', 'ug_user_id')->references('user_id')->on('sys_user')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sys_user_group', function (Blueprint $table) {
            $table->dropForeign('ug_group_id');
            $table->dropForeign('ug_user_id');
        });
    }
}
