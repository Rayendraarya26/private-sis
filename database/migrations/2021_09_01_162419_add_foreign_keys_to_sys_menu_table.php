<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSysMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sys_menu', function (Blueprint $table) {
            $table->foreign('menu_parent_id', 'menu_parent_id')->references('menu_id')->on('sys_menu')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sys_menu', function (Blueprint $table) {
            $table->dropForeign('menu_parent_id');
        });
    }
}
