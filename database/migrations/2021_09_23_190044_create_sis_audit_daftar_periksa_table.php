<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSisAuditDaftarPeriksaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sis_audit_daftar_periksa', function (Blueprint $table) {
            $table->bigInteger('dftr_periksa_id', true);
            $table->bigInteger('jadw_id')->index('FK_sis_audit_daftar_periksa__sis_jadwal');
            $table->string('dftr_periksa_file')->nullable();
            $table->string('dftr_periksa_oleh')->nullable();
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
        Schema::dropIfExists('sis_audit_daftar_periksa');
    }
}
