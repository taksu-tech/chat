<?php

namespace Taksu\TaksuChat\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ChatRoomParticipant extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    public function newUniqueId()
    {
        return 'crp-'.strtolower((string) Str::ulid());
    }

    protected $fillable = [
        'chat_room_id',
        'participant_id',
        'participant_type',
        'last_read',
    ];

    protected $casts = [
        'last_read' => 'datetime',
    ];

    public function participant(): MorphTo
    {
        return $this->morphTo();
    }

    public function room()
    {
        return $this->belongsTo(ChatRoom::class, 'chat_room_id');
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return \Taksu\TaksuChat\Factories\ChatRoomParticipantFactory::new();
    }
}
