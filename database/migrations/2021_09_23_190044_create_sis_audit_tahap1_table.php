<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSisAuditTahap1Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sis_audit_tahap1', function (Blueprint $table) {
            $table->bigInteger('aud_thp1_id', true);
            $table->bigInteger('jadw_audit_id')->index('FK_sis_audit_tahap1__sis_jadwal_audit');
            $table->enum('aud_thp1_status_audit', ['accepted', 'rejected', 'on-progress'])->nullable()->default('on-progress');
            $table->text('aud_thp1_kesimpulan')->nullable();
            $table->text('aud_thp1_rekomendasi')->nullable();
            $table->string('aud_thp1_ditijau_oleh')->nullable();
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
        Schema::dropIfExists('sis_audit_tahap1');
    }
}
