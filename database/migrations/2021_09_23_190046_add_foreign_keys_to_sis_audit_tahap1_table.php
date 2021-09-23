<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSisAuditTahap1Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sis_audit_tahap1', function (Blueprint $table) {
            $table->foreign('jadw_audit_id', 'FK_sis_audit_tahap1__sis_jadwal_audit')->references('jadw_audit_id')->on('sis_jadwal_audit')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sis_audit_tahap1', function (Blueprint $table) {
            $table->dropForeign('FK_sis_audit_tahap1__sis_jadwal_audit');
        });
    }
}
