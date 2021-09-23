<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSisPelangganSertifikasiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sis_pelanggan_sertifikasi', function (Blueprint $table) {
            $table->bigInteger('cust_sert_id')->primary();
            $table->smallInteger('sert_id')->index('FK_sis_pelanggan_sertifikasi__ref_sertifikasi');
            $table->bigInteger('cust_id')->index('FK_sis_pelanggan_sertifikasi__sis_pelanggan');
            $table->string('cust_sert_nomor_sertifikat')->nullable();
            $table->string('cust_sert_nomor_referensi')->nullable();
            $table->string('cust_sert_nomor_sni')->nullable();
            $table->text('cust_sert_lingkup')->nullable();
            $table->string('kode_ea_nama')->nullable();
            $table->string('kode_nace_nama')->nullable();
            $table->integer('komodt_id')->nullable();
            $table->string('cust_sert_tipe')->nullable();
            $table->string('cust_sert_merk')->nullable();
            $table->date('cust_sert_tgl_sertifikat_awal')->nullable();
            $table->date('cust_sert_tgl_sertifikat_perubahan')->nullable();
            $table->enum('cust_sert_status', ['on_going', 'expired', 'dibekukan'])->nullable()->default('on_going');
            $table->date('cust_sert_expired_date');
            $table->enum('cust_sert_status_survailen', ['passed', 'on-progress', 'rejected'])->nullable()->default('passed');
            $table->string('cust_sert_filepath')->nullable();
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
        Schema::dropIfExists('sis_pelanggan_sertifikasi');
    }
}
