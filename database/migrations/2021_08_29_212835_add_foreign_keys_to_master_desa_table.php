<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToMasterDesaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_desa', function (Blueprint $table) {
            $table->foreign('kec_id', 'master_desa_ibfk_1')->references('kec_id')->on('master_kecamatan')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_desa', function (Blueprint $table) {
            $table->dropForeign('master_desa_ibfk_1');
        });
    }
}
