<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSisAuditObservasiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sis_audit_observasi', function (Blueprint $table) {
            $table->bigInteger('obsvasi_id', true);
            $table->bigInteger('jadw_id')->index('FK_sis_audit_observasi__sis_jadwal');
            $table->text('obsvasi_uraian')->nullable();
            $table->date('obsvasi_tgl')->nullable();
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
        Schema::dropIfExists('sis_audit_observasi');
    }
}
