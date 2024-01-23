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
        Schema::create('chat_room_participants', function (Blueprint $table) {
            $table->string('id', 30)->primary();

            $table->string('chat_room_id', 30);
            $table->string('participant_type', 35);
            $table->string('participant_id', 40);
            $table->dateTime('last_read')->nullable();

            $table->string('created_by', 30)->nullable();
            $table->string('updated_by', 30)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_room_participants');
    }
};
