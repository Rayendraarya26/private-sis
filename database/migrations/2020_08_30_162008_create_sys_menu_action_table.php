<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSysMenuActionTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'sys_menu_action';

    /**
     * Run the migrations.
     * @table sys_menu_action
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('action_id');
            $table->unsignedInteger('action_menu_id');
            $table->string('action_name');
            $table->string('action_controller');
            $table->timestamp('action_created_at')->nullable()->useCurrent();
            $table->timestamp('action_updated_at')->nullable()->default(null);

            $table->index(["action_menu_id"], 'action_menu_id');


            $table->foreign('action_menu_id', 'action_menu_id')
                ->references('menu_id')->on('sys_menu')
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
