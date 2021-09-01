<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterIconsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_icons', function (Blueprint $table) {
            $table->bigIncrements('icon_id');
            $table->string('icon_name')->nullable();
            $table->timestamp('icon_created_at')->nullable()->useCurrent();
            $table->timestamp('icon_updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_icons');
    }
}
