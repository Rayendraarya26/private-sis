<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSisPermohonanKomoditiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sis_permohonan_komoditi', function (Blueprint $table) {
            $table->foreign('komodt_id', 'FK_sis_permohonan_komoditi__master_komoditi')->references('komodt_id')->on('master_komoditi')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('mohon_id', 'FK_sis_permohonan_komoditi__sis_permohonan')->references('mohon_id')->on('sis_permohonan')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sis_permohonan_komoditi', function (Blueprint $table) {
            $table->dropForeign('FK_sis_permohonan_komoditi__master_komoditi');
            $table->dropForeign('FK_sis_permohonan_komoditi__sis_permohonan');
        });
    }
}
