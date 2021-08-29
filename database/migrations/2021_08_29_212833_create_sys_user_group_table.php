<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSysUserGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_user_group', function (Blueprint $table) {
            $table->unsignedBigInteger('ug_user_id')->index('ug_user_id');
            $table->unsignedInteger('ug_group_id')->index('ug_group_id');
            $table->enum('ug_is_default', ['yes', 'no'])->nullable()->default('no');
            $table->timestamp('ug_created_at')->nullable()->useCurrent();
            $table->timestamp('ug_updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_user_group');
    }
}
