<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSisPelangganDokumenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sis_pelanggan_dokumen', function (Blueprint $table) {
            $table->bigInteger('cust_dok_id', true);
            $table->bigInteger('cust_id')->nullable()->index('FK_sis_pelanggan_dokumen__sis_pelanggan');
            $table->smallInteger('jenis_dok_perusahaan_id')->nullable()->index('FK_sis_pelanggan_dokumen__ref_jenis_dok_perusahaan');
            $table->text('cust_dok_deskripsi')->nullable();
            $table->text('cust_dok_filepath')->nullable();
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
        Schema::dropIfExists('sis_pelanggan_dokumen');
    }
}
