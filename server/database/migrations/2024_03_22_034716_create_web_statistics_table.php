<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('web_statistics', function (Blueprint $table) {
            $table->id();
            $table->integer('total_users')->default(0);
            $table->integer('total_sessions')->default(0);
            $table->integer('total_sessions_duration')->default(0);
            $table->integer('total_requests')->default(0);

            $table->integer('total_songs')->default(0);
            $table->integer('total_user_upload_songs')->default(0);
            $table->integer('total_played_time')->default(0);
            $table->integer('total_played_duration')->default(0);

            $table->integer('total_albums')->default(0);
            $table->integer('total_artists')->default(0);
            $table->integer('total_playlists')->default(0);
            $table->integer('total_genres')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_statistics');
    }
};
