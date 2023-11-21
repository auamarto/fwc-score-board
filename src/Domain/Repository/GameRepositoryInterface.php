<?php

namespace FwcScoreBoard\Domain\Repository;

use FwcScoreBoard\Domain\Model\GameEventInterface;
use FwcScoreBoard\Domain\Model\GamesSummary;
use Ramsey\Uuid\UuidInterface;

interface GameRepositoryInterface
{
    public function createGame(UuidInterface $gameId, string $homeTeam, string $awayTeam): void;

    public function addEvent(UuidInterface $gameId, GameEventInterface $event): void;

    public function getSummary(): GamesSummary;
}
