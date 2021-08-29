<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterJenisDokPerusahaanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_jenis_dok_perusahaan', function (Blueprint $table) {
            $table->smallInteger('jenis_dok_perusahaan_id', true);
            $table->string('jenis_dok_perusahaan_text')->nullable();
            $table->text('jenis_dok_perusahaan_deskripsi')->nullable();
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
        Schema::dropIfExists('master_jenis_dok_perusahaan');
    }
}
