<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSisRegisterPelangganTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sis_register_pelanggan', function (Blueprint $table) {
            $table->bigInteger('reg_id', true);
            $table->string('reg_email')->nullable();
            $table->string('reg_nomor_telp')->nullable();
            $table->string('reg_nomor_fax')->nullable();
            $table->string('reg_nomor_hp')->nullable();
            $table->string('reg_nama')->nullable();
            $table->smallInteger('jenis_perusahaan_id')->nullable()->index('FK_sis_register_pelanggan__ref_jenis_perusahaan');
            $table->smallInteger('badan_hukum_id')->nullable()->index('FK_sis_register_pelanggan__ref_badan_hukum');
            $table->integer('kec_id')->nullable()->index('FK_sis_register_pelanggan__ref_kecamatan');
            $table->smallInteger('kab_id')->nullable()->index('FK_sis_register_pelanggan__ref_kabupaten');
            $table->smallInteger('prov_id')->nullable()->index('FK_sis_register_pelanggan__ref_provinsi');
            $table->text('reg_alamat')->nullable();
            $table->enum('reg_status', ['none', 'draft', 'success'])->default('none');
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
        Schema::dropIfExists('sis_register_pelanggan');
    }
}
