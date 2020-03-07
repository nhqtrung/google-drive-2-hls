<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    
    
        Schema::create('videos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('input_path');
            $table->string('input_disk');
            $table->string('output_path');
            $table->string('output_disk');
            $table->string('google_drive_folder');
            $table->string('watermark')->nullable();
            $table->string('stream_link')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('export_progress', function (Blueprint $table) {
            $table->engine = "MEMORY";
            $table->increments('id');
            $table->integer('percentent_progress')->nullable();
            $table->integer('idVideo')->unsigned();
            $table->foreign('idVideo')->references('id')->on('videos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('export-progress');

        Schema::dropIfExists('videos');
    }
}
