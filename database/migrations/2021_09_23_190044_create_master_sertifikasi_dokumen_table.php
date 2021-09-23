<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterSertifikasiDokumenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_sertifikasi_dokumen', function (Blueprint $table) {
            $table->integer('sert_dok_id', true);
            $table->smallInteger('jenis_dok_perusahaan_id');
            $table->smallInteger('sert_id')->index('FK_master_sertifikasi_dokumen__master_sertifikasi');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->unique(['jenis_dok_perusahaan_id', 'sert_id'], 'UNIQUE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_sertifikasi_dokumen');
    }
}
