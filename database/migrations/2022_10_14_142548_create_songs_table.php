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
            $table->string("song_id");
            $table->string("user_id");
            $table->string("album_id")->nullable();
            $table->string("title");
            $table->string("normalized_title");
            $table->integer("listens")->default(0);
            $table->string("display")->default("private");
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
