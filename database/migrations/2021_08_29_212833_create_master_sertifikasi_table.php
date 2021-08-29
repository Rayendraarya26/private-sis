<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterSertifikasiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_sertifikasi', function (Blueprint $table) {
            $table->smallInteger('sert_id', true);
            $table->string('sert_nama')->nullable();
            $table->text('sert_deskripsi')->nullable();
            $table->smallInteger('sert_expired')->nullable()->comment('dalam bulan');
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
        Schema::dropIfExists('master_sertifikasi');
    }
}
