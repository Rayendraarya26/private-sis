<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterPertanyaanPermohonanJawabanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_pertanyaan_permohonan_jawaban', function (Blueprint $table) {
            $table->bigInteger('tanya_jwb_id', true);
            $table->integer('tanya_mohon_id')->nullable()->index('FK_master_pertanyaan_permohonan_jawaban__pertanyaan_permohonan');
            $table->smallInteger('tanya_jwb_urut')->nullable();
            $table->string('tanya_jwb_text')->nullable();
            $table->decimal('tanya_jwb_score', 10, 0)->nullable();
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
        Schema::dropIfExists('master_pertanyaan_permohonan_jawaban');
    }
}
