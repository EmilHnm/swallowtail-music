<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('song_files', function (Blueprint $table) {
            $table->id()->startingValue(1000000000);
            $table->string("song_id")
                ->nullable()
                ->unique();
            $table->timestamps();
            $table->string("file_path")->nullable();
            $table->string("driver")->nullable();
            $table->string("status")->nullable();
            $table->bson("lyrics")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('song_files');
    }
};
