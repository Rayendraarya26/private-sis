<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSisJadwalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sis_jadwal', function (Blueprint $table) {
            $table->bigInteger('jadw_id', true);
            $table->string('jadw_bil_nomor')->nullable();
            $table->double('jadw_bil_total')->nullable();
            $table->enum('jadw_bil_status', ['proses', 'lunas'])->nullable();
            $table->string('jadw_bil_invoice')->nullable()->comment('file invoice');
            $table->string('jadw_bil_bukti_bayar')->nullable()->comment('file bukti pembayaran');
            $table->enum('jadw_tanggal_status', ['on-going', 'rejected', 'accepted'])->nullable()->default('on-going');
            $table->date('jadw_tanggal_mulai');
            $table->date('jadw_tanggal_selesai')->nullable();
            $table->enum('jadw_jenis', ['tunggal', 'kombinasi', 'gabung', 'integrasi'])->nullable()->default('tunggal');
            $table->enum('jadw_team_status', ['on-going', 'rejected', 'accepted'])->nullable()->default('on-going');
            $table->text('jadw_team_alasan')->nullable();
            $table->bigInteger('cust_id')->index('FK_sis_jadwal__sis_pelanggan');
            $table->string('jadw_file_jadwal')->nullable();
            $table->enum('jadw_status_audit', ['on-going', 'accepted', 'rejected'])->nullable()->default('on-going');
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
        Schema::dropIfExists('sis_jadwal');
    }
}
