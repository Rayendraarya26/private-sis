<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSysUserNotifTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_user_notif', function (Blueprint $table) {
            $table->bigIncrements('notif_id');
            $table->unsignedBigInteger('notif_user_id')->nullable()->index('notif_user_id');
            $table->string('notif_title')->nullable();
            $table->string('notif_content')->nullable();
            $table->string('notif_link')->nullable();
            $table->enum('notif_is_read', ['yes', 'no'])->nullable()->default('no');
            $table->timestamp('notif_created_at')->nullable()->useCurrent();
            $table->timestamp('notif_updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_user_notif');
    }
}
