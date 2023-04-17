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
        Schema::create("songs", function (Blueprint $table) {
            $table->id();
            $table
                ->string("song_id")
                ->nullable()
                ->unique();
            $table->string("user_id")->nullable();
            $table->string("album_id")->nullable();
            $table->string("title")->nullable();
            $table->string("sub_title")->nullable();
            $table->string("file_path")->nullable();
            $table->integer("listens")->nullable();
            $table->integer("duration")->nullable();
            $table->string("display")->nullable();
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
        Schema::dropIfExists("songs");
    }
};
