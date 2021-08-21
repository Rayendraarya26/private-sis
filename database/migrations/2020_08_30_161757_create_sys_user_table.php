<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSysUserTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'sys_user';

    /**
     * Run the migrations.
     * @table sys_user
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('user_id');
            $table->string('user_email');
            $table->string('user_fullname')->nullable()->default(null);
            $table->string('user_password')->nullable()->default(null);
            $table->string('user_token')->nullable()->default(null);
            $table->enum('user_is_active', ['yes', 'no'])->default('yes');
            $table->enum('user_is_banned', ['yes', 'no'])->default('no');
            $table->string('user_picture')->nullable()->default('/images/profiles/default.png');
            $table->timestamp('user_last_login')->nullable()->default(null);
            $table->timestamp('user_active_at')->nullable()->default(null);
            $table->timestamp('user_banned_at')->nullable()->default(null);
            $table->timestamp('user_created_at')->nullable()->useCurrent();
            $table->timestamp('user_updated_at')->nullable()->default(null);

            $table->unique(["user_email"], 'user_email');
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
