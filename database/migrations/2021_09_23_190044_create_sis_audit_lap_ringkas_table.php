<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSisAuditLapRingkasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sis_audit_lap_ringkas', function (Blueprint $table) {
            $table->bigInteger('lap_ringkas_id', true);
            $table->bigInteger('jadw_id')->index('FK_sis_audit_lap_ringkas__sis_jadwal');
            $table->text('lap_ringkas_kesimpulan')->nullable();
            $table->text('lap_ringkas_rekomendasi')->nullable();
            $table->string('lap_ringkas_filepath')->nullable();
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
        Schema::dropIfExists('sis_audit_lap_ringkas');
    }
}
