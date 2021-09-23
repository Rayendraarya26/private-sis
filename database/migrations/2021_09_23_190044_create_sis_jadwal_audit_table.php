<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSisJadwalAuditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sis_jadwal_audit', function (Blueprint $table) {
            $table->bigInteger('jadw_audit_id', true);
            $table->bigInteger('jadw_id')->nullable()->index('FK_sis_jadwal_audit__sis_jadwal');
            $table->enum('jadw_audit_jenis', ['survailen', 'sertifikasi', 're-sertifikasi'])->nullable();
            $table->bigInteger('mohon_id')->nullable()->index('FK_sis_jadwal_audit__sis_permohonan_sertifikasi');
            $table->smallInteger('sert_id')->nullable();
            $table->integer('komodt_id')->nullable();
            $table->bigInteger('cust_sert_id')->nullable();
            $table->enum('jadw_audit_is_tahap1', ['ya', 'tidak'])->nullable()->default('tidak');
            $table->string('jadw_audit_nomor_referensi')->nullable();
            $table->string('jadw_audit_kode_nace')->nullable();
            $table->string('jadw_audit_standart_acuan')->nullable();
            $table->string('jadw_audit_ruang_lingkup')->nullable();
            $table->string('jadw_audit_kegiatan')->nullable();
            $table->string('jadw_audit_tujuan_audit')->nullable();
            $table->enum('jadw_audit_sertifikat_status', ['on-going', 'accepted', 'rejected'])->nullable()->default('on-going');
            $table->string('jadw_audit_sertifikat_filepath')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
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
        Schema::dropIfExists('sis_jadwal_audit');
    }
}
