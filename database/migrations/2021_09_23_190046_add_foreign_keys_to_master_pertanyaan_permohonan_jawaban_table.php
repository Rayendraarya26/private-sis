<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToMasterPertanyaanPermohonanJawabanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_pertanyaan_permohonan_jawaban', function (Blueprint $table) {
            $table->foreign('tanya_mohon_id', 'FK_master_pertanyaan_permohonan_jawaban__pertanyaan_permohonan')->references('tanya_mohon_id')->on('master_pertanyaan_permohonan')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_pertanyaan_permohonan_jawaban', function (Blueprint $table) {
            $table->dropForeign('FK_master_pertanyaan_permohonan_jawaban__pertanyaan_permohonan');
        });
    }
}
