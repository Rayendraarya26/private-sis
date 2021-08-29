<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterKabupatenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_kabupaten', function (Blueprint $table) {
            $table->smallInteger('kab_id')->primary();
            $table->smallInteger('prov_id')->index('FK_ref_kabupaten__ref_provinsi');
            $table->string('kab_nama')->index('INDEX_1');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_kabupaten');
    }
}
