<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSysGroupPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_group_permission', function (Blueprint $table) {
            $table->bigIncrements('permission_id');
            $table->unsignedInteger('group_id');
            $table->unsignedInteger('action_id')->index('action_id');
            $table->unique(['group_id', 'action_id'], 'group_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_group_permission');
    }
}
