<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSisAuditSertifikatProdukTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sis_audit_sertifikat_produk', function (Blueprint $table) {
            $table->bigInteger('prod_sert_id', true);
            $table->bigInteger('jadw_id')->index('FK_sis_audit_sertifikat_produk__sis_jadwal');
            $table->string('prod_sert_nama')->nullable();
            $table->string('prod_sert_filepath')->nullable();
            $table->text('prod_sert_keterangan')->nullable();
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
        Schema::dropIfExists('sis_audit_sertifikat_produk');
    }
}
