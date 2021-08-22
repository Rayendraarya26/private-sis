<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogEmailOutboxTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_log')->create('log_email_outbox', function (Blueprint $table) {
            $table->bigIncrements('outbox_id');
            $table->text('outbox_uuid');
            $table->string('outbox_reply_to')->nullable();
            $table->string('outbox_from_name')->nullable();
            $table->string('outbox_from_email')->nullable();
            $table->string('outbox_to_name')->nullable();
            $table->string('outbox_to_email')->nullable();
            $table->text('outbox_title')->nullable();
            $table->text('outbox_message')->nullable();
            $table->enum('outbox_read', ['no', 'yes'])->default('no');
            $table->timestamp('outbox_read_at')->nullable();
            $table->enum('outbox_type', ['system', 'scheduler'])->default('system');
            $table->timestamp('outbox_created_at')->nullable()->useCurrent();
            $table->timestamp('outbox_updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_log')->dropIfExists('log_email_outbox');
    }
}
