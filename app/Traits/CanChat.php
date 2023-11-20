<?php

namespace Taksu\TaksuChat\Traits;

trait CanChat
{
    public function getType(): string
    {
        return get_class($this);
    }

    public function getId(): string
    {
        return $this->id;
    }
}
