<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSisPermohonanDokumenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sis_permohonan_dokumen', function (Blueprint $table) {
            $table->foreign('mohon_id', 'FK_sis_permohonan_dokumen__sis_permohonan_pelanggan')->references('mohon_id')->on('sis_permohonan')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sis_permohonan_dokumen', function (Blueprint $table) {
            $table->dropForeign('FK_sis_permohonan_dokumen__sis_permohonan_pelanggan');
        });
    }
}
