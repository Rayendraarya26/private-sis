<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSisAuditTimKomiteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sis_audit_tim_komite', function (Blueprint $table) {
            $table->bigInteger('komite_id', true);
            $table->bigInteger('jadw_id')->index('FK_sis_audit_tim_komite__sis_jadwal');
            $table->bigInteger('peg_id')->index('FK_sis_audit_tim_komite__master_pegawai');
            $table->enum('komite_posisi', ['ketua', 'anggota'])->default('anggota');
            $table->enum('komite_penetapan', ['pemberian', 'pengunaan', 'pencabutan'])->nullable()->default('pemberian');
            $table->date('komite_tgl_surat')->nullable();
            $table->date('komite_tgl_kesanggupan')->nullable();
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
        Schema::dropIfExists('sis_audit_tim_komite');
    }
}
