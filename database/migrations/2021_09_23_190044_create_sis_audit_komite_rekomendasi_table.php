<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSisAuditKomiteRekomendasiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sis_audit_komite_rekomendasi', function (Blueprint $table) {
            $table->bigInteger('rekmd_komte_id', true);
            $table->bigInteger('jadw_id')->unique('UNIQUE');
            $table->text('rekmd_komte_isi')->nullable();
            $table->text('rekmd_komte_kronologin')->nullable();
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
        Schema::dropIfExists('sis_audit_komite_rekomendasi');
    }
}
