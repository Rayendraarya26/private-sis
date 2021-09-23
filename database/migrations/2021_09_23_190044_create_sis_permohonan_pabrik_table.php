<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSisPermohonanPabrikTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sis_permohonan_pabrik', function (Blueprint $table) {
            $table->bigInteger('mohon_pabrik_id', true);
            $table->bigInteger('mohon_id')->index('FK_sis_permohonan_pabrik__sis_permohonan_pelanggan');
            $table->string('mohon_pabrik_nomor_telp')->nullable();
            $table->string('mohon_pabrik_nomor_fax')->nullable();
            $table->string('mohon_pabrik_nomor_hp')->nullable();
            $table->string('mohon_pabrik_nama')->nullable();
            $table->integer('kec_id')->nullable()->index('FK_sis_permohonan_pabrik__ref_kecamatan');
            $table->smallInteger('kab_id')->nullable()->index('FK_sis_permohonan_pabrik__ref_kabupaten');
            $table->smallInteger('prov_id')->nullable()->index('FK_sis_permohonan_pabrik__ref_provinsi');
            $table->text('mohon_pabrik_alamat')->nullable();
            $table->string('mohon_pabrik_kode_pos')->nullable();
            $table->smallInteger('mohon_pabrik_jumlah_karyawan')->nullable();
            $table->text('mohon_pabrik_kegiatan_utama')->nullable();
            $table->string('mohon_pabrik_luas_tanah')->nullable();
            $table->string('mohon_pabrik_luas_bangunan')->nullable();
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
        Schema::dropIfExists('sis_permohonan_pabrik');
    }
}
