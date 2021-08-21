<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSysUserNotifTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'sys_user_notif';

    /**
     * Run the migrations.
     * @table sys_nofif
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('notif_id');
            $table->unsignedBigInteger('notif_user_id')->nullable()->default(null);
            $table->string('notif_title')->nullable()->default(null);
            $table->string('notif_content')->nullable()->default(null);
            $table->string('notif_link')->nullable()->default(null);
            $table->enum('notif_is_read', ['yes', 'no'])->nullable()->default('no');
            $table->timestamp('notif_created_at')->nullable()->useCurrent();
            $table->timestamp('notif_updated_at')->nullable()->default(null);

            $table->index(["notif_user_id"], 'notif_user_id');

            $table->foreign('notif_user_id', 'notif_user_id')
                ->references('user_id')->on('sys_user')
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
