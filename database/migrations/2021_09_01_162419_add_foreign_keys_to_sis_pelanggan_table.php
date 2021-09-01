<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSisPelangganTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sis_pelanggan', function (Blueprint $table) {
            $table->foreign('badan_hukum_id', 'FK_sis_pelanggan__ref_badan_hukum')->references('badan_hukum_id')->on('master_badan_hukum')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('jenis_perusahaan_id', 'FK_sis_pelanggan__ref_jenis_perusahaan')->references('jenis_perusahaan_id')->on('master_jenis_perusahaan')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('kab_id', 'FK_sis_pelanggan__ref_kabupaten')->references('kab_id')->on('master_kabupaten')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('kec_id', 'FK_sis_pelanggan__ref_kecamatan')->references('kec_id')->on('master_kecamatan')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('prov_id', 'FK_sis_pelanggan__ref_provinsi')->references('prov_id')->on('master_provinsi')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('reg_id', 'FK_sis_pelanggan__sis_register_pelanggan')->references('reg_id')->on('sis_register_pelanggan')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('user_id', 'FK_sis_pelanggan__sys_user')->references('user_id')->on('sys_user')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sis_pelanggan', function (Blueprint $table) {
            $table->dropForeign('FK_sis_pelanggan__ref_badan_hukum');
            $table->dropForeign('FK_sis_pelanggan__ref_jenis_perusahaan');
            $table->dropForeign('FK_sis_pelanggan__ref_kabupaten');
            $table->dropForeign('FK_sis_pelanggan__ref_kecamatan');
            $table->dropForeign('FK_sis_pelanggan__ref_provinsi');
            $table->dropForeign('FK_sis_pelanggan__sis_register_pelanggan');
            $table->dropForeign('FK_sis_pelanggan__sys_user');
        });
    }
}
