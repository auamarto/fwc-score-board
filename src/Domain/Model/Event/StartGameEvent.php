<?php

namespace FwcScoreBoard\Domain\Model\Event;

use FwcScoreBoard\Domain\Model\GameEventInterface;

class StartGameEvent implements GameEventInterface
{
    private readonly int $timestamp;

    public function __construct()
    {
        $this->timestamp = time();
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }
}
