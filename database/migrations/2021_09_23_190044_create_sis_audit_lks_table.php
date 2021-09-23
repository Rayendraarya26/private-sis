<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSisAuditLksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sis_audit_lks', function (Blueprint $table) {
            $table->bigInteger('lks_id', true);
            $table->bigInteger('jadw_id')->index('FK_sis_audit_lks__sis_jadwal');
            $table->unsignedBigInteger('user_id')->index('FK_sis_audit_lks__sys_user');
            $table->string('jadw_team_kode')->nullable();
            $table->text('lks_uraian_ketidaksesuaian')->nullable();
            $table->enum('lks_kategori_ketidaksesuaian', ['kritis', 'mayor', 'minor', 'observasi'])->nullable();
            $table->text('lks_klausul_ketidaksesuaian')->nullable();
            $table->text('lks_perbaikan_analisa')->nullable();
            $table->text('lks_perbaikan_koreksi')->nullable();
            $table->text('lks_perbaikan_tindakan')->nullable();
            $table->text('lks_bagian_pendamping')->nullable();
            $table->text('lks_bukti_tindakan_perbaikan')->nullable();
            $table->date('lks_expired_date_perbaikan')->nullable();
            $table->date('lks_input_date_perbaikan')->nullable();
            $table->enum('lks_status', ['memadai', 'tidak-memadai', 'revisi'])->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
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
        Schema::dropIfExists('sis_audit_lks');
    }
}
