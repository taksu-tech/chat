<?php

namespace Taksu\TaksuChat\Seeders;

use Illuminate\Database\Seeder;
use Taksu\TaksuChat\Models\ChatMessage;

class ChatMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 3; $i++) {
            ChatMessage::factory()->create();
        }
    }
}
