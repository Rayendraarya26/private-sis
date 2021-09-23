<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToMasterPegawaiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_pegawai', function (Blueprint $table) {
            $table->foreign('user_id', 'FK_master_pegawai__sys_user')->references('user_id')->on('sys_user')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_pegawai', function (Blueprint $table) {
            $table->dropForeign('FK_master_pegawai__sys_user');
        });
    }
}
