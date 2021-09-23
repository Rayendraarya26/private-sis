<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSisAuditLksRevisiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sis_audit_lks_revisi', function (Blueprint $table) {
            $table->foreign('lks_id', 'FK_sis_audit_lks_revisi__sis_audit_lks')->references('lks_id')->on('sis_audit_lks')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sis_audit_lks_revisi', function (Blueprint $table) {
            $table->dropForeign('FK_sis_audit_lks_revisi__sis_audit_lks');
        });
    }
}
