<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSisAuditKomitePeriksaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sis_audit_komite_periksa', function (Blueprint $table) {
            $table->bigInteger('komte_priksa_id', true);
            $table->bigInteger('jadw_id')->unique('UNIQUE');
            $table->text('komte_priksa_penilaian_1')->nullable();
            $table->text('komte_priksa_penilaian_2')->nullable();
            $table->text('komte_priksa_penilaian_3')->nullable();
            $table->text('komte_priksa_penilaian_4')->nullable();
            $table->text('komte_priksa_penilaian_5')->nullable();
            $table->text('komte_priksa_penilaian_6')->nullable();
            $table->text('komte_priksa_penilaian_7')->nullable();
            $table->text('komte_priksa_penilaian_8')->nullable();
            $table->text('komte_priksa_penilaian_9')->nullable();
            $table->text('komte_priksa_penilaian_10')->nullable();
            $table->text('komte_priksa_penilaian_11')->nullable();
            $table->text('komte_priksa_penilaian_12')->nullable();
            $table->text('komte_priksa_penilaian_13')->nullable();
            $table->text('komte_priksa_keputusan')->nullable();
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
        Schema::dropIfExists('sis_audit_komite_periksa');
    }
}
