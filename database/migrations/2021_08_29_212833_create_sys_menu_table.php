<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSysMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_menu', function (Blueprint $table) {
            $table->increments('menu_id');
            $table->unsignedInteger('menu_parent_id')->nullable();
            $table->string('menu_name')->nullable();
            $table->string('menu_desc')->nullable();
            $table->enum('menu_is_active', ['yes', 'no'])->default('yes');
            $table->string('menu_icon')->nullable();
            $table->integer('menu_order')->nullable()->default(1);
            $table->timestamp('menu_created_at')->nullable()->useCurrent();
            $table->timestamp('menu_updated_at')->nullable();
            $table->unique(['menu_parent_id', 'menu_name', 'menu_is_active'], 'menu_parent_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_menu');
    }
}
