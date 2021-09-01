<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSisPermohonanSertifikasiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sis_permohonan_sertifikasi', function (Blueprint $table) {
            $table->bigInteger('req_sert_id', true);
            $table->unsignedBigInteger('cust_id');
            $table->bigInteger('sert_id');
            $table->enum('req_sert_status', ['none'])->default('none');
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
        Schema::dropIfExists('sis_permohonan_sertifikasi');
    }
}
