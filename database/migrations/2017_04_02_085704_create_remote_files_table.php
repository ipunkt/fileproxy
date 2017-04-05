<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRemoteFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('remote_files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('proxy_file_id');
            $table->text('url');
            $table->text('options')->nullable()->default(null);// http request options
            $table->string('path')->nullable()->default(null);// local file path for caching
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('remote_files');
    }
}
