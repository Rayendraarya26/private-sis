<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSysGroupTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'sys_group';

    /**
     * Run the migrations.
     * @table sys_group
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('group_id');
            $table->string('group_name');
            $table->string('group_desc')->nullable()->default(null);
            $table->enum('group_is_active', ['yes', 'no'])->default('yes');
            $table->timestamp('group_created_at')->nullable()->useCurrent();
            $table->timestamp('group_updated_at')->nullable()->default(null);
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
