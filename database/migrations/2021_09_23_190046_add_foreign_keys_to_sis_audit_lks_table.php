<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSisAuditLksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sis_audit_lks', function (Blueprint $table) {
            $table->foreign('jadw_id', 'FK_sis_audit_lks__sis_jadwal')->references('jadw_id')->on('sis_jadwal')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('user_id', 'FK_sis_audit_lks__sys_user')->references('user_id')->on('sys_user')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sis_audit_lks', function (Blueprint $table) {
            $table->dropForeign('FK_sis_audit_lks__sis_jadwal');
            $table->dropForeign('FK_sis_audit_lks__sys_user');
        });
    }
}
