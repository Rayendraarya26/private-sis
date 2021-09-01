<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSisPelangganDokumenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sis_pelanggan_dokumen', function (Blueprint $table) {
            $table->foreign('jenis_dok_perusahaan_id', 'FK_sis_pelanggan_dokumen__ref_jenis_dok_perusahaan')->references('jenis_dok_perusahaan_id')->on('master_jenis_dok_perusahaan')->onUpdate('SET NULL')->onDelete('SET NULL');
            $table->foreign('cust_id', 'FK_sis_pelanggan_dokumen__sis_pelanggan')->references('cust_id')->on('sis_pelanggan')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sis_pelanggan_dokumen', function (Blueprint $table) {
            $table->dropForeign('FK_sis_pelanggan_dokumen__ref_jenis_dok_perusahaan');
            $table->dropForeign('FK_sis_pelanggan_dokumen__sis_pelanggan');
        });
    }
}
