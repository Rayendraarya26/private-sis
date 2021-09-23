<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSisPermohonanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sis_permohonan', function (Blueprint $table) {
            $table->foreign('sert_id', 'FK_sis_permohonan__master_sertifikasi')->references('sert_id')->on('master_sertifikasi')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('cust_id', 'FK_sis_permohonan__sis_pelanggan')->references('cust_id')->on('sis_pelanggan')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('cust_sert_id', 'FK_sis_permohonan__sis_pelanggan_sertifikasi')->references('cust_sert_id')->on('sis_pelanggan_sertifikasi')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('user_id', 'FK_sis_permohonan__sys_user')->references('user_id')->on('sys_user')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sis_permohonan', function (Blueprint $table) {
            $table->dropForeign('FK_sis_permohonan__master_sertifikasi');
            $table->dropForeign('FK_sis_permohonan__sis_pelanggan');
            $table->dropForeign('FK_sis_permohonan__sis_pelanggan_sertifikasi');
            $table->dropForeign('FK_sis_permohonan__sys_user');
        });
    }
}
