<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSisPelangganTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sis_pelanggan', function (Blueprint $table) {
            $table->bigInteger('cust_id', true);
            $table->unsignedBigInteger('user_id')->unique('UNIQUE');
            $table->string('cust_email')->nullable();
            $table->string('cust_nomor_telp')->nullable();
            $table->string('cust_nomor_fax')->nullable();
            $table->string('cust_nomor_hp')->nullable();
            $table->string('cust_nama')->nullable();
            $table->smallInteger('jenis_perusahaan_id')->nullable()->index('FK_sis_pelanggan__ref_jenis_perusahaan');
            $table->smallInteger('badan_hukum_id')->nullable()->index('FK_sis_pelanggan__ref_badan_hukum');
            $table->enum('cust_asing', ['ya', 'tidak'])->nullable()->default('tidak');
            $table->integer('negara_id')->nullable();
            $table->integer('kec_id')->nullable()->index('FK_sis_pelanggan__ref_kecamatan');
            $table->smallInteger('kab_id')->nullable()->index('FK_sis_pelanggan__ref_kabupaten');
            $table->smallInteger('prov_id')->nullable()->index('FK_sis_pelanggan__ref_provinsi');
            $table->text('cust_alamat')->nullable();
            $table->string('cust_nomor_akta_pendirian')->nullable();
            $table->string('cust_nama_pemilik')->nullable();
            $table->string('cust_nama_pimpinan')->nullable();
            $table->string('cust_nama_wakil_manajemen')->nullable();
            $table->smallInteger('cust_jumlah_bagian')->nullable();
            $table->smallInteger('cust_jumlah_manajemen')->nullable();
            $table->smallInteger('cust_jumlah_administrasi')->nullable();
            $table->smallInteger('cust_jumlah_part_time')->nullable();
            $table->smallInteger('cust_jumlah_operasional')->nullable();
            $table->smallInteger('cust_jumlah_shift_1')->nullable();
            $table->smallInteger('cust_jumlah_shift_2')->nullable();
            $table->smallInteger('cust_jumlah_shift_3')->nullable();
            $table->smallInteger('cust_jumlah_non_permanen')->nullable();
            $table->smallInteger('cust_shif_kerja')->nullable();
            $table->string('cust_luas_tanah')->nullable();
            $table->string('cust_luas_bangunan')->nullable();
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
        Schema::dropIfExists('sis_pelanggan');
    }
}
