<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSisJadwalTimTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sis_jadwal_tim', function (Blueprint $table) {
            $table->bigInteger('jadw_tim_id', true);
            $table->bigInteger('jadw_id')->index('FK_sis_jadwal_tim__sis_jadwal');
            $table->bigInteger('peg_id')->index('FK_sis_jadwal_tim__master_pegawai');
            $table->string('jadw_tim_kode')->nullable();
            $table->enum('jadw_tim_posisi', ['ketua', 'auditor', 'ppc', 'observer'])->nullable()->default('observer');
            $table->enum('jadw_tim_kesanggupan', ['none', 'ya', 'tidak'])->nullable()->default('none');
            $table->date('jadw_tim_kesanggupan_tgl')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sis_jadwal_tim');
    }
}
