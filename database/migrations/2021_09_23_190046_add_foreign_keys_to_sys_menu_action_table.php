<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSysMenuActionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sys_menu_action', function (Blueprint $table) {
            $table->foreign('action_menu_id', 'action_menu_id')->references('menu_id')->on('sys_menu')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sys_menu_action', function (Blueprint $table) {
            $table->dropForeign('action_menu_id');
        });
    }
}
