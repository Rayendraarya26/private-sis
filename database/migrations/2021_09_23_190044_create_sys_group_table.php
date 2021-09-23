<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSysGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_group', function (Blueprint $table) {
            $table->increments('group_id');
            $table->string('group_name');
            $table->string('group_desc')->nullable();
            $table->enum('group_is_active', ['yes', 'no'])->default('yes');
            $table->timestamp('group_created_at')->nullable()->useCurrent();
            $table->timestamp('group_updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_group');
    }
}
