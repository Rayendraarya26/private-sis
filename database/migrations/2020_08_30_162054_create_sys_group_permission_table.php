<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSysGroupPermissionTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'sys_group_permission';

    /**
     * Run the migrations.
     * @table sys_group_permission
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('permission_id');
            $table->unsignedInteger('group_id');
            $table->unsignedInteger('action_id');

            $table->index(["action_id"], 'action_id');

            $table->unique(["group_id", "action_id"], 'group_id');


            $table->foreign('group_id', 'group_id')
                ->references('group_id')->on('sys_group')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('action_id', 'action_id')
                ->references('action_id')->on('sys_menu_action')
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
        Schema::dropIfExists($this->tableName);
    }
}
