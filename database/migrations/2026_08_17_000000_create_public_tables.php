<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublicTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('public_profil_perusahaan')) {
            Schema::create('public_profil_perusahaan', function (Blueprint $table) {
                $table->increments('profil_id');
                $table->string('profil_fullname_perusahaan')->nullable();
                $table->string('profil_shortname_perusahaan')->nullable();
                $table->text('profil_desc_perusahaan')->nullable();
                $table->text('profil_alamat_perusahaan')->nullable();
                $table->string('profil_email_perusahaan')->nullable();
                $table->string('profil_fax_perusahaan')->nullable();
                $table->string('profil_telp_perusahaan')->nullable();
                $table->string('profil_whatsapp_perusahaan')->nullable();
                $table->string('profil_fullname_app')->nullable();
                $table->string('profil_shortname_app')->nullable();
                $table->string('profil_app_icon')->nullable();
                $table->text('profil_app_desc')->nullable();
                $table->string('profil_background_image')->nullable();
                $table->string('profil_ketidakperpihakan_file')->nullable();
            });
        }

        if (!Schema::hasTable('public_lembaga')) {
            Schema::create('public_lembaga', function (Blueprint $table) {
                $table->increments('lem_id');
                $table->string('lem_name')->nullable();
                $table->text('lem_desc')->nullable();
                $table->text('lem_content')->nullable();
                $table->string('lem_external_link')->nullable();
                $table->boolean('lem_status')->default(true)->nullable();
            });
        }

        if (!Schema::hasTable('public_sop')) {
            Schema::create('public_sop', function (Blueprint $table) {
                $table->increments('sop_id');
                $table->string('sop_name')->nullable();
                $table->text('sop_desc')->nullable();
                $table->string('sop_image')->nullable();
                $table->boolean('sop_status')->default(true)->nullable();
            });
        }

        if (!Schema::hasTable('public_social_media')) {
            Schema::create('public_social_media', function (Blueprint $table) {
                $table->increments('socmed_id');
                $table->string('socmed_name')->nullable();
                $table->string('socmed_icon_cls')->nullable();
                $table->string('socmed_link')->nullable();
                $table->boolean('socmed_status')->default(true)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('public_social_media');
        Schema::dropIfExists('public_sop');
        Schema::dropIfExists('public_lembaga');
        Schema::dropIfExists('public_profil_perusahaan');
    }
}
