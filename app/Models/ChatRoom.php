<?php

namespace Taksu\TaksuChat\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ChatRoom extends Model
{
    use HasFactory, HasUlids;

    const STATUS_OPEN = 'open';

    const STATUS_CLOSED = 'closed';

    const TYPE_PRIVATE = 'private';

    const TYPE_GROUP = 'group';

    public function newUniqueId()
    {
        return 'cr-'.strtolower((string) Str::ulid());
    }

    protected $fillable = [
        'name',
        'description',
        'type',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public static function getSearchable(): array
    {
        return ['name'];
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function lastMessage()
    {
        return $this->hasOne(ChatMessage::class)->latest();
    }

    public function participants()
    {
        return $this->hasMany(ChatRoomParticipant::class);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return \Taksu\TaksuChat\Factories\ChatRoomFactory::new();
    }
}
