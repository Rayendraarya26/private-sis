<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSisPermohonanKomoditiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sis_permohonan_komoditi', function (Blueprint $table) {
            $table->bigInteger('mohon_kmditi_id')->primary();
            $table->bigInteger('mohon_id')->index('FK_sis_permohonan_komoditi__sis_permohonan');
            $table->integer('komodt_id')->index('FK_sis_permohonan_komoditi__master_komoditi');
            $table->string('mohon_kmditi_sni')->nullable();
            $table->string('mohon_kmditi_merk')->nullable();
            $table->string('mohon_kmditi_tipe')->nullable();
            $table->string('mohon_kmditi_ukuran')->nullable();
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
        Schema::dropIfExists('sis_permohonan_komoditi');
    }
}
