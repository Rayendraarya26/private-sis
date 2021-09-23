<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSisPermohonanJawabanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sis_permohonan_jawaban', function (Blueprint $table) {
            $table->foreign('tanya_mohon_id', 'FK_sis_permohonan_jawaban__master_pertanyaan_permohonan')->references('tanya_mohon_id')->on('master_pertanyaan_permohonan')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('mohon_id', 'FK_sis_permohonan_jawaban__sis_permohonan_pelanggan')->references('mohon_id')->on('sis_permohonan')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sis_permohonan_jawaban', function (Blueprint $table) {
            $table->dropForeign('FK_sis_permohonan_jawaban__master_pertanyaan_permohonan');
            $table->dropForeign('FK_sis_permohonan_jawaban__sis_permohonan_pelanggan');
        });
    }
}
