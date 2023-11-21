<?php

namespace FwcScoreBoard\Domain\Model\Event;

use FwcScoreBoard\Domain\Model\GameEventInterface;

class ScoreUpdateEvent implements GameEventInterface
{
    private readonly int $timestamp;

    public function __construct(
        private readonly int $homeTeamScore = 0,
        private readonly int $awayTeamScore = 0,
    ) {
        $this->timestamp = time();
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getHomeTeamScore(): int
    {
        return $this->homeTeamScore;
    }

    public function getAwayTeamScore(): int
    {
        return $this->awayTeamScore;
    }
}
