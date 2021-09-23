<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterSertifikasiKlausulTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_sertifikasi_klausul', function (Blueprint $table) {
            $table->bigInteger('sert_klau_id', true);
            $table->smallInteger('sert_id')->index('FK_master_sertifikasi_klausul__master_sertifikasi');
            $table->char('sert_klau_nomor', 1)->nullable();
            $table->string('sert_klau_peryataan')->nullable();
            $table->enum('sert_klau_is_item', ['ya', 'tidak'])->nullable()->default('ya');
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
        Schema::dropIfExists('master_sertifikasi_klausul');
    }
}
