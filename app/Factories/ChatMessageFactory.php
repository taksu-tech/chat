<?php

namespace Taksu\TaksuChat\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Taksu\TaksuChat\Models\ChatMessage;
use Taksu\TaksuChat\Models\ChatRoom;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Taksu\TaksuChat\Models\ChatMessage>
 */
class ChatMessageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = ChatMessage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (new ChatMessage)->newUniqueId(),
            'chat_room_id' => ChatRoom::factory(),
            'sender_type' => 'App\Models\Client',
            'sender_id' => fake()->uuid(),
            'message' => fake()->sentence(10),
            'media_mime' => 'image/png',
            'media_url' => 'https://placehold.co/600x400/png',
        ];
    }
}
