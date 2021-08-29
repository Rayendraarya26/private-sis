<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToMasterKecamatanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_kecamatan', function (Blueprint $table) {
            $table->foreign('kab_id', 'FK_ref_kecamatan__ref_kabupaten')->references('kab_id')->on('master_kabupaten')->onUpdate('CASCADE')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_kecamatan', function (Blueprint $table) {
            $table->dropForeign('FK_ref_kecamatan__ref_kabupaten');
        });
    }
}
