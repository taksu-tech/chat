<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ChatMessage extends Model implements HasMedia
{
    use HasFactory, HasUlids, InteractsWithMedia;

    public function newUniqueId()
    {
        return 'cm-'.strtolower((string) Str::ulid());
    }

    protected $fillable = [
        'chat_room_id',
        'type',
        'message',
        'sender_type',
        'sender_id',
    ];

    public function sender(): MorphTo
    {
        return $this->morphTo();
    }

    public function room()
    {
        return $this->belongsTo(ChatRoom::class, 'chat_room_id');
    }
}
