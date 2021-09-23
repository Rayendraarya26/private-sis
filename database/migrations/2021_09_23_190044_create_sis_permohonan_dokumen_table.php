<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSisPermohonanDokumenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sis_permohonan_dokumen', function (Blueprint $table) {
            $table->bigInteger('mohon_dok_id', true);
            $table->bigInteger('mohon_id')->index('FK_sis_permohonan_dokumen__sis_permohonan_pelanggan');
            $table->smallInteger('jenis_dok_perusahaan_id')->nullable();
            $table->text('mohon_dok_deskripsi')->nullable();
            $table->text('mohon_dok_filepath')->nullable();
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
        Schema::dropIfExists('sis_permohonan_dokumen');
    }
}
