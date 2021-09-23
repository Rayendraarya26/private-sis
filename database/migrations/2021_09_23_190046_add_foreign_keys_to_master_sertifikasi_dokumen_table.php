<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToMasterSertifikasiDokumenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_sertifikasi_dokumen', function (Blueprint $table) {
            $table->foreign('jenis_dok_perusahaan_id', 'FK_master_sertifikasi_dokumen__master_jenis_dok_perusahaan')->references('jenis_dok_perusahaan_id')->on('master_jenis_dok_perusahaan')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('sert_id', 'FK_master_sertifikasi_dokumen__master_sertifikasi')->references('sert_id')->on('master_sertifikasi')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_sertifikasi_dokumen', function (Blueprint $table) {
            $table->dropForeign('FK_master_sertifikasi_dokumen__master_jenis_dok_perusahaan');
            $table->dropForeign('FK_master_sertifikasi_dokumen__master_sertifikasi');
        });
    }
}
