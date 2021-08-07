<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSysUserGroupTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'sys_user_group';

    /**
     * Run the migrations.
     * @table sys_user_group
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->unsignedBigInteger('ug_user_id');
            $table->unsignedInteger('ug_group_id');
            $table->enum('ug_is_default', ['yes', 'no'])->nullable()->default('no');
            $table->timestamp('ug_created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('ug_updated_at')->nullable()->default(null);

            $table->index(["ug_user_id"], 'ug_user_id');

            $table->index(["ug_group_id"], 'ug_group_id');


            $table->foreign('ug_user_id', 'ug_user_id')
                ->references('user_id')->on('sys_user')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('ug_group_id', 'ug_group_id')
                ->references('group_id')->on('sys_group')
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
