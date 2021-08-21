<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterEmailTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_email_template', function (Blueprint $table) {
            $table->bigIncrements('template_id');
            $table->string('template_uuid');
            $table->string("template_code");
            $table->string("template_desc")->nullable();
            $table->string("template_mail_subject")->nullable();
            $table->text("template_mail_body")->nullable();
            $table->timestamp('template_created_at')->nullable()->useCurrent();
            $table->timestamp('template_updated_at')->nullable();

            $table->unique(["template_uuid"]);
            $table->unique(["template_code"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_email_template');
    }
}
