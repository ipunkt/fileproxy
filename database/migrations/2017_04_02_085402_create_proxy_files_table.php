<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProxyFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proxy_files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('reference', 36)->unique();
            $table->enum('type', ['remote', 'local']);
            $table->string('filename');
            $table->string('mimetype');//  application/pdf, ...
            $table->unsignedBigInteger('size');// size in bytes
            $table->string('checksum');// hash/checksum of file
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
        Schema::dropIfExists('proxy_files');
    }
}
