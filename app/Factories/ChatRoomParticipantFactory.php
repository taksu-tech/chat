<?php

namespace Taksu\TaksuChat\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Taksu\TaksuChat\Models\ChatRoom;
use Taksu\TaksuChat\Models\ChatRoomParticipant;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Taksu\TaksuChat\Models\ChatRoomParticipant>
 */
class ChatRoomParticipantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = ChatRoomParticipant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (new ChatRoomParticipant)->newUniqueId(),
            'chat_room_id' => ChatRoom::factory(),
            'participant_type' => '\App\Models\Client',
            'participant_id' => fake()->uuid(),
            'last_read' => now(),
        ];
    }
}
