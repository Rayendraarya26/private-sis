<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSisPelangganPabrikTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sis_pelanggan_pabrik', function (Blueprint $table) {
            $table->bigInteger('pabrik_id', true);
            $table->bigInteger('cust_id')->nullable()->index('FK_sis_pelanggan_pabrik__sis_pelanggan');
            $table->string('pabrik_nomor_telp')->nullable();
            $table->string('pabrik_nomor_fax')->nullable();
            $table->string('pabrik_nomor_hp')->nullable();
            $table->string('pabrik_nama')->nullable();
            $table->integer('kec_id')->nullable()->index('FK_sis_pelanggan_pabrik__ref_kecamatan');
            $table->smallInteger('kab_id')->nullable()->index('FK_sis_pelanggan_pabrik__ref_kabupaten');
            $table->smallInteger('prov_id')->nullable()->index('FK_sis_pelanggan_pabrik__ref_provinsi');
            $table->text('pabrik_alamat')->nullable();
            $table->string('pabrik_kode_pos')->nullable();
            $table->smallInteger('pabrik_jumlah_karyawan')->nullable();
            $table->text('pabrik_kegiatan_utama')->nullable();
            $table->string('pabrik_luas_tanah')->nullable();
            $table->string('pabrik_luas_bangunan')->nullable();
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
        Schema::dropIfExists('sis_pelanggan_pabrik');
    }
}
