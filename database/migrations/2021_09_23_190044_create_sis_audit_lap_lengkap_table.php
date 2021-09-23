<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSisAuditLapLengkapTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sis_audit_lap_lengkap', function (Blueprint $table) {
            $table->bigInteger('lap_lengkp_id', true);
            $table->bigInteger('jadw_id')->index('FK_sis_audit_lap_lengkap__sis_jadwal');
            $table->text('lap_lengkp_penilaian')->nullable();
            $table->text('lap_lengkp_penyimpangan')->nullable();
            $table->text('lap_lengkp_isu_berdampak')->nullable();
            $table->text('lap_lengkp_isu_tidak_terselesaikan')->nullable();
            $table->text('lap_lengkp_perubahan')->nullable();
            $table->text('lap_lengkp_kekuatan')->nullable();
            $table->text('lap_lengkp_kelemahan')->nullable();
            $table->text('lap_lengkp_tinjauan_keluhan')->nullable();
            $table->text('lap_lengkp_pengendalian_penggunaan')->nullable();
            $table->text('lap_lengkp_kedalaman_audit')->nullable();
            $table->text('lap_lengkp_pernyataan_kesesuaian')->nullable();
            $table->text('lap_lengkp_kesimpulan_ketaatan')->nullable();
            $table->text('lap_lengkp_konfirmasi_tujuan')->nullable();
            $table->text('lap_lengkp_saran')->nullable();
            $table->text('lap_lengkp_kesimpulan')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sis_audit_lap_lengkap');
    }
}
