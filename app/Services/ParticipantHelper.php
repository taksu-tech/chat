<?php

namespace Taksu\TaksuChat\Services;

use Taksu\TaksuChat\Traits\CanChat;

class ParticipantHelper
{
    use CanChat;

    public function __construct(
        private string $id,
        private string $type,
    ) {
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
