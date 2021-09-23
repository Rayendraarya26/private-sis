<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSysMenuActionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_menu_action', function (Blueprint $table) {
            $table->increments('action_id');
            $table->unsignedInteger('action_menu_id')->index('action_menu_id');
            $table->string('action_name');
            $table->string('action_controller');
            $table->timestamp('action_created_at')->nullable()->useCurrent();
            $table->timestamp('action_updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_menu_action');
    }
}
