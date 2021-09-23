<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSisAuditDetailTahap1Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sis_audit_detail_tahap1', function (Blueprint $table) {
            $table->bigInteger('aud_thp1_det_id', true);
            $table->bigInteger('aud_thp1_id')->index('FK_sis_audit_detail_tahap1__sis_audit_tahap1');
            $table->bigInteger('klausul_thp1_id')->nullable();
            $table->string('aud_thp1_det_thp1_nomor')->nullable();
            $table->text('aud_thp1_det_peryataan')->nullable();
            $table->enum('aud_thp1_det_is_tinjauan', ['ya', 'tidak'])->nullable()->default('tidak');
            $table->string('aud_thp1_det_kode_dok')->nullable();
            $table->string('aud_thp1_det_judul_dok')->nullable();
            $table->enum('aud_thp1_det_hasil_tinjauan', ['ok', 'no'])->nullable()->default('ok');
            $table->text('aud_thp1_det_keterangan')->nullable();
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
        Schema::dropIfExists('sis_audit_detail_tahap1');
    }
}
