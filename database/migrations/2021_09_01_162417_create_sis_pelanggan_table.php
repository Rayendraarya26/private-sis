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
            $table->bigInteger('reg_id')->nullable()->index('FK_sis_pelanggan__sis_register_pelanggan');
            $table->unsignedBigInteger('user_id')->unique('UNIQUE');
            $table->string('cust_email')->nullable();
            $table->string('cust_nomor_telp')->nullable();
            $table->string('cust_nomor_fax')->nullable();
            $table->string('cust_nomor_hp')->nullable();
            $table->string('cust_nama')->nullable();
            $table->smallInteger('jenis_perusahaan_id')->nullable()->index('FK_sis_pelanggan__ref_jenis_perusahaan');
            $table->smallInteger('badan_hukum_id')->nullable()->index('FK_sis_pelanggan__ref_badan_hukum');
            $table->integer('kec_id')->nullable()->index('FK_sis_pelanggan__ref_kecamatan');
            $table->smallInteger('kab_id')->nullable()->index('FK_sis_pelanggan__ref_kabupaten');
            $table->smallInteger('prov_id')->nullable()->index('FK_sis_pelanggan__ref_provinsi');
            $table->text('cust_alamat')->nullable();
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
