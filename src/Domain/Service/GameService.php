<?php

namespace FwcScoreBoard\Domain\Service;

use FwcScoreBoard\Domain\Model\Event\FinishGameEvent;
use FwcScoreBoard\Domain\Model\Event\ScoreUpdateEvent;
use FwcScoreBoard\Domain\Model\Event\StartGameEvent;
use FwcScoreBoard\Domain\Repository\GameRepositoryInterface;
use Ramsey\Uuid\UuidInterface;

class GameService
{
    public function __construct(
        private readonly GameRepositoryInterface $gameRepository,
    ) {
    }

    public function startGame(UuidInterface $gameId, string $homeTeam, string $awayTeam): void
    {
        $this->gameRepository->createGame($gameId, $homeTeam, $awayTeam);
        $this->gameRepository->addEvent($gameId, new StartGameEvent());
        $this->gameRepository->addEvent($gameId, new ScoreUpdateEvent());
    }

    public function finishGame(UuidInterface $gameId): void
    {
        $this->gameRepository->addEvent($gameId, new FinishGameEvent());
    }

    public function updateScore(UuidInterface $gameId, int $homeTeamScore, int $awayTeamScore): void
    {
        $this->gameRepository->addEvent($gameId, new ScoreUpdateEvent($homeTeamScore, $awayTeamScore));
    }
}
