<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterPegawaiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_pegawai', function (Blueprint $table) {
            $table->bigInteger('peg_id', true);
            $table->unsignedBigInteger('user_id')->index('FK_master_pegawai__sys_user');
            $table->string('peg_nama');
            $table->text('peg_alamat')->nullable();
            $table->string('peg_telp')->nullable();
            $table->string('peg_ttd_file')->nullable();
            $table->enum('peg_status', ['aktif', 'non-aktif'])->default('aktif');
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
        Schema::dropIfExists('master_pegawai');
    }
}
