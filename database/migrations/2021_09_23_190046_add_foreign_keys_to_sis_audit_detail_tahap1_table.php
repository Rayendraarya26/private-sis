<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSisAuditDetailTahap1Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sis_audit_detail_tahap1', function (Blueprint $table) {
            $table->foreign('aud_thp1_id', 'FK_sis_audit_detail_tahap1__sis_audit_tahap1')->references('aud_thp1_id')->on('sis_audit_tahap1')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sis_audit_detail_tahap1', function (Blueprint $table) {
            $table->dropForeign('FK_sis_audit_detail_tahap1__sis_audit_tahap1');
        });
    }
}
