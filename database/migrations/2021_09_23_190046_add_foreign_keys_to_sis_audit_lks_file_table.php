<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSisAuditLksFileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sis_audit_lks_file', function (Blueprint $table) {
            $table->foreign('lks_id', 'FK_sis_audit_lks_file__sis_audit_lks')->references('lks_id')->on('sis_audit_lks')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sis_audit_lks_file', function (Blueprint $table) {
            $table->dropForeign('FK_sis_audit_lks_file__sis_audit_lks');
        });
    }
}
