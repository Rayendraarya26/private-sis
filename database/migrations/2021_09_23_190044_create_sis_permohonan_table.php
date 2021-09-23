<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSisPermohonanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sis_permohonan', function (Blueprint $table) {
            $table->bigInteger('mohon_id', true);
            $table->bigInteger('cust_id')->index('FK_sis_permohonan__sis_pelanggan');
            $table->unsignedBigInteger('user_id')->index('FK_sis_permohonan__sys_user');
            $table->smallInteger('sert_id')->index('FK_sis_permohonan__master_sertifikasi');
            $table->enum('mohon_approved_status', ['on-progress', 'rejected', 'accepted'])->default('on-progress');
            $table->enum('mohon_jenis_status', ['baru', 'lama'])->default('baru');
            $table->bigInteger('cust_sert_id')->nullable()->index('FK_sis_permohonan__sis_pelanggan_sertifikasi');
            $table->string('mohon_kajian_permohonan_file')->nullable();
            $table->string('mohon_pernyataan_persetujuan_file')->nullable();
            $table->string('mohon_spk_file')->nullable();
            $table->enum('mohon_harus_lunas_status', ['ya', 'tidak'])->nullable()->default('tidak');
            $table->enum('mohon_pembayaran_status', ['proses', 'lunas'])->nullable()->default('proses');
            $table->string('mohon_cust_email')->nullable();
            $table->string('mohon_cust_nomor_telp')->nullable();
            $table->string('mohon_cust_nomor_fax')->nullable();
            $table->string('mohon_cust_nomor_hp')->nullable();
            $table->string('mohon_cust_nama')->nullable();
            $table->smallInteger('jenis_perusahaan_id')->nullable();
            $table->smallInteger('badan_hukum_id')->nullable();
            $table->enum('cust_asing', ['ya', 'tidak'])->nullable()->default('tidak');
            $table->integer('negara_id')->nullable();
            $table->integer('kec_id')->nullable();
            $table->smallInteger('kab_id')->nullable();
            $table->smallInteger('prov_id')->nullable();
            $table->text('mohon_cust_alamat')->nullable();
            $table->string('mohon_cust_nomor_akta_pendirian')->nullable();
            $table->string('mohon_cust_nama_pemilik')->nullable();
            $table->string('mohon_cust_nama_pimpinan')->nullable();
            $table->string('mohon_cust_nama_wakil_manajemen')->nullable();
            $table->smallInteger('mohon_cust_jumlah_bagian')->nullable();
            $table->smallInteger('mohon_cust_jumlah_manajemen')->nullable();
            $table->smallInteger('mohon_cust_jumlah_administrasi')->nullable();
            $table->smallInteger('mohon_cust_jumlah_part_time')->nullable();
            $table->smallInteger('mohon_cust_jumlah_operasional')->nullable();
            $table->smallInteger('mohon_cust_jumlah_shift_1')->nullable();
            $table->smallInteger('mohon_cust_jumlah_shift_2')->nullable();
            $table->smallInteger('mohon_cust_jumlah_shift_3')->nullable();
            $table->smallInteger('mohon_cust_jumlah_non_permanen')->nullable();
            $table->smallInteger('mohon_cust_shif_kerja')->nullable();
            $table->string('mohon_cust_luas_tanah')->nullable();
            $table->string('mohon_cust_luas_bangunan')->nullable();
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
        Schema::dropIfExists('sis_permohonan');
    }
}
