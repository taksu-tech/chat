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
        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->string('id', 30)->primary();

            $table->string('status', 20);
            $table->string('name', 50)->nullable();
            $table->text('description')->nullable();
            $table->dateTime('last_message_at')->default(now());

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
        Schema::dropIfExists('chat_rooms');
    }
};
