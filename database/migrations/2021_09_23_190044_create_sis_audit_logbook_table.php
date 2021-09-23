<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSisAuditLogbookTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sis_audit_logbook', function (Blueprint $table) {
            $table->bigInteger('logbook_id', true);
            $table->bigInteger('jadw_id')->index('FK_sis_audit_logbook__sis_jadwal');
            $table->string('logbook_filepath')->nullable();
            $table->enum('logbook_jenis', ['ppc', 'auditor'])->nullable();
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
        Schema::dropIfExists('sis_audit_logbook');
    }
}
