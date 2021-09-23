<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSisAuditLksRevisiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sis_audit_lks_revisi', function (Blueprint $table) {
            $table->bigInteger('lks_revisi_id')->primary();
            $table->bigInteger('lks_id')->index('FK_sis_audit_lks_revisi__sis_audit_lks');
            $table->text('lks_revisi_catatan')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sis_audit_lks_revisi');
    }
}
