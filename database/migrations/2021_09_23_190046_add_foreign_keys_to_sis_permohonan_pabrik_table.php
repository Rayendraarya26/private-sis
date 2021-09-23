<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSisPermohonanPabrikTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sis_permohonan_pabrik', function (Blueprint $table) {
            $table->foreign('kab_id', 'FK_sis_permohonan_pabrik__ref_kabupaten')->references('kab_id')->on('master_kabupaten')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('kec_id', 'FK_sis_permohonan_pabrik__ref_kecamatan')->references('kec_id')->on('master_kecamatan')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('prov_id', 'FK_sis_permohonan_pabrik__ref_provinsi')->references('prov_id')->on('master_provinsi')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('mohon_id', 'FK_sis_permohonan_pabrik__sis_permohonan_pelanggan')->references('mohon_id')->on('sis_permohonan')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sis_permohonan_pabrik', function (Blueprint $table) {
            $table->dropForeign('FK_sis_permohonan_pabrik__ref_kabupaten');
            $table->dropForeign('FK_sis_permohonan_pabrik__ref_kecamatan');
            $table->dropForeign('FK_sis_permohonan_pabrik__ref_provinsi');
            $table->dropForeign('FK_sis_permohonan_pabrik__sis_permohonan_pelanggan');
        });
    }
}
