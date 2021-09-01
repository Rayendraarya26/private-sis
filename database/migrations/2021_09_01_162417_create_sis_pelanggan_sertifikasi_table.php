<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSisPelangganSertifikasiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sis_pelanggan_sertifikasi', function (Blueprint $table) {
            $table->bigInteger('cust_sert_id')->primary();
            $table->smallInteger('sert_id')->index('FK_sis_pelanggan_sertifikasi__ref_sertifikasi');
            $table->bigInteger('cust_id')->index('FK_sis_pelanggan_sertifikasi__sis_pelanggan');
            $table->enum('cust_sert_status', ['on_going', 'expired'])->nullable()->default('on_going');
            $table->date('cust_sert_expired_date');
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
        Schema::dropIfExists('sis_pelanggan_sertifikasi');
    }
}
