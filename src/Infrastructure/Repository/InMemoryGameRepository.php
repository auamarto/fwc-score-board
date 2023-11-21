<?php

namespace FwcScoreBoard\Infrastructure\Repository;

use FwcScoreBoard\Domain\Model\Game;
use FwcScoreBoard\Domain\Model\GameEventInterface;
use FwcScoreBoard\Domain\Model\GamesSummary;
use FwcScoreBoard\Domain\Repository\GameRepositoryInterface;
use Ramsey\Uuid\UuidInterface;

class InMemoryGameRepository implements GameRepositoryInterface
{
    /**
     * @param Game[] $games
     * @param GameEventInterface[][] $events
     */
    public function __construct(
        private array &$games = [],
        private array &$events = [],
    ) {
    }

    public function createGame(UuidInterface $gameId, string $homeTeam, string $awayTeam): void
    {
        $this->games[] = new Game($gameId, $homeTeam, $awayTeam, []);
    }

    public function addEvent(UuidInterface $gameId, GameEventInterface $event): void
    {
        $this->events[$gameId->toString()][] = $event;
    }

    public function getSummary(): GamesSummary
    {
        $games = [];
        foreach ($this->games as $game) {
            $games[] = new Game(
                $game->getId(),
                $game->getHomeTeam(),
                $game->getAwayTeam(),
                $this->events[$game->getId()->toString()]
            );
        }

        return new GamesSummary($games);
    }
}
