<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSisJadwalTimTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sis_jadwal_tim', function (Blueprint $table) {
            $table->foreign('peg_id', 'FK_sis_jadwal_tim__master_pegawai')->references('peg_id')->on('master_pegawai')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('jadw_id', 'FK_sis_jadwal_tim__sis_jadwal')->references('jadw_id')->on('sis_jadwal')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sis_jadwal_tim', function (Blueprint $table) {
            $table->dropForeign('FK_sis_jadwal_tim__master_pegawai');
            $table->dropForeign('FK_sis_jadwal_tim__sis_jadwal');
        });
    }
}
