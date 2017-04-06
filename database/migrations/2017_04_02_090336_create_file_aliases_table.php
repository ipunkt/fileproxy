<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileAliasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_aliases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('proxy_file_id');
            $table->string('path')->unique();
            $table->unsignedBigInteger('hits_left')->nullable()->default(null);
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable()->default(null);
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
        Schema::dropIfExists('file_aliases');
    }
}
