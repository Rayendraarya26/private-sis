<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterKlausulTahap1Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_klausul_tahap1', function (Blueprint $table) {
            $table->bigInteger('klausul_thp1_id', true);
            $table->smallInteger('sert_id')->nullable();
            $table->char('klausul_thp1_nomor')->nullable();
            $table->text('klausul_thp1_peryataan')->nullable();
            $table->enum('klausul_thp1_is_tinjauan', ['ya', 'tidak'])->nullable()->default('tidak');
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
        Schema::dropIfExists('master_klausul_tahap1');
    }
}
