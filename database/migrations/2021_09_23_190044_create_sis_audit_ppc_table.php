<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSisAuditPpcTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sis_audit_ppc', function (Blueprint $table) {
            $table->bigInteger('audit_ppc_id', true);
            $table->bigInteger('jadw_id')->index('FK_sis_ppc_report__sis_jadwal');
            $table->enum('audit_ppc_jenis_file', ['19', '20', '21'])->nullable();
            $table->string('audit_ppc_filepath')->nullable();
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
        Schema::dropIfExists('sis_audit_ppc');
    }
}
