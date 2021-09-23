<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterPertanyaanPermohonanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_pertanyaan_permohonan', function (Blueprint $table) {
            $table->integer('tanya_mohon_id', true);
            $table->smallInteger('tanya_mohon_urut')->nullable();
            $table->text('tanya_mohon_pertanyaan')->nullable();
            $table->enum('tanya_mohon_jenis_jawaban', ['deskriptif', 'pilihan'])->nullable()->default('deskriptif');
            $table->enum('tanya_mohon_status', ['aktif', 'non-aktif'])->nullable()->default('aktif');
            $table->timestamp('created_at')->nullable()->useCurrent();
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
        Schema::dropIfExists('master_pertanyaan_permohonan');
    }
}
