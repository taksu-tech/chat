<?php

namespace Taksu\TaksuChat\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Taksu\TaksuChat\Models\ChatRoom;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Taksu\TaksuChat\Models\ChatRoom>
 */
class ChatRoomFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = ChatRoom::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (new ChatRoom)->newUniqueId(),
            'name' => fake()->text(10),
            'status' => fake()->randomElement([
                ChatRoom::STATUS_OPEN,
                ChatRoom::STATUS_CLOSED,
            ]),
            'type' => fake()->randomElement([
                ChatRoom::TYPE_GROUP,
                ChatRoom::TYPE_PRIVATE,
            ]),
            'description' => fake()->sentence(10),
            'last_message_at' => now(),
            'is_read_only' => false,
        ];
    }
}
