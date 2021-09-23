<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSisJadwalAuditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sis_jadwal_audit', function (Blueprint $table) {
            $table->foreign('jadw_id', 'FK_sis_jadwal_audit__sis_jadwal')->references('jadw_id')->on('sis_jadwal')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('mohon_id', 'FK_sis_jadwal_audit__sis_permohonan_sertifikasi')->references('mohon_id')->on('sis_permohonan')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sis_jadwal_audit', function (Blueprint $table) {
            $table->dropForeign('FK_sis_jadwal_audit__sis_jadwal');
            $table->dropForeign('FK_sis_jadwal_audit__sis_permohonan_sertifikasi');
        });
    }
}
