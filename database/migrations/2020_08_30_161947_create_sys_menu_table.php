<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSysMenuTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'sys_menu';

    /**
     * Run the migrations.
     * @table sys_menu
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('menu_id');
            $table->unsignedInteger('menu_parent_id')->nullable()->default(null);
            $table->string('menu_name')->nullable()->default(null);
            $table->string('menu_desc')->nullable()->default(null);
            $table->enum('menu_is_active', ['yes', 'no'])->default('yes');
            $table->string('menu_icon')->nullable()->default(null);
            $table->integer('menu_order')->nullable()->default('1');
            $table->timestamp('menu_created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('menu_updated_at')->nullable()->default(null);

            $table->unique(["menu_parent_id", "menu_name", "menu_is_active"], 'menu_parent_id');


            $table->foreign('menu_parent_id', 'menu_parent_id')
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
