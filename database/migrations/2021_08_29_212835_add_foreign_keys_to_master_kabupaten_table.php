<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToMasterKabupatenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_kabupaten', function (Blueprint $table) {
            $table->foreign('prov_id', 'FK_ref_kabupaten__ref_provinsi')->references('prov_id')->on('master_provinsi')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_kabupaten', function (Blueprint $table) {
            $table->dropForeign('FK_ref_kabupaten__ref_provinsi');
        });
    }
}
