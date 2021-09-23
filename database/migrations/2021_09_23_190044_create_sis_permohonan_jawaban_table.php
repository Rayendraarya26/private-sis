<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSisPermohonanJawabanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sis_permohonan_jawaban', function (Blueprint $table) {
            $table->bigInteger('mohon_jawab_id')->primary();
            $table->bigInteger('mohon_id')->index('FK_sis_permohonan_jawaban__sis_permohonan_pelanggan');
            $table->integer('tanya_mohon_id')->index('FK_sis_permohonan_jawaban__master_pertanyaan_permohonan');
            $table->text('mohon_jawab_jawaban')->nullable();
            $table->decimal('mohon_jawab_score', 10, 0)->nullable();
            $table->bigInteger('tanya_jwb_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
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
        Schema::dropIfExists('sis_permohonan_jawaban');
    }
}
