<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToMasterSertifikasiKlausulTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_sertifikasi_klausul', function (Blueprint $table) {
            $table->foreign('sert_id', 'FK_master_sertifikasi_klausul__master_sertifikasi')->references('sert_id')->on('master_sertifikasi')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_sertifikasi_klausul', function (Blueprint $table) {
            $table->dropForeign('FK_master_sertifikasi_klausul__master_sertifikasi');
        });
    }
}
