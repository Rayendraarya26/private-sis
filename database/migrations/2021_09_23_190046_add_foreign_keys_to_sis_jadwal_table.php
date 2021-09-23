<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSisJadwalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sis_jadwal', function (Blueprint $table) {
            $table->foreign('cust_id', 'FK_sis_jadwal__sis_pelanggan')->references('cust_id')->on('sis_pelanggan')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sis_jadwal', function (Blueprint $table) {
            $table->dropForeign('FK_sis_jadwal__sis_pelanggan');
        });
    }
}
