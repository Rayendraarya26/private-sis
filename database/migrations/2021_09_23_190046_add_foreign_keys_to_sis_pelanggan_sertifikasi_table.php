<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSisPelangganSertifikasiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sis_pelanggan_sertifikasi', function (Blueprint $table) {
            $table->foreign('sert_id', 'FK_sis_pelanggan_sertifikasi__ref_sertifikasi')->references('sert_id')->on('master_sertifikasi')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('cust_id', 'FK_sis_pelanggan_sertifikasi__sis_pelanggan')->references('cust_id')->on('sis_pelanggan')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sis_pelanggan_sertifikasi', function (Blueprint $table) {
            $table->dropForeign('FK_sis_pelanggan_sertifikasi__ref_sertifikasi');
            $table->dropForeign('FK_sis_pelanggan_sertifikasi__sis_pelanggan');
        });
    }
}
