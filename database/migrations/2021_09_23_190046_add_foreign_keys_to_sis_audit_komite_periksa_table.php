<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSisAuditKomitePeriksaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sis_audit_komite_periksa', function (Blueprint $table) {
            $table->foreign('jadw_id', 'FK_sis_audit_komite_periksa__sis_jadwal')->references('jadw_id')->on('sis_jadwal')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sis_audit_komite_periksa', function (Blueprint $table) {
            $table->dropForeign('FK_sis_audit_komite_periksa__sis_jadwal');
        });
    }
}
