<?php

namespace Domain\Service;

use FwcScoreBoard\Domain\Model\Event\FinishGameEvent;
use FwcScoreBoard\Domain\Model\Event\ScoreUpdateEvent;
use FwcScoreBoard\Domain\Model\Event\StartGameEvent;
use FwcScoreBoard\Domain\Model\Game;
use FwcScoreBoard\Domain\Service\GameService;
use FwcScoreBoard\Infrastructure\Repository\InMemoryGameRepository;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class GameServiceTest extends TestCase
{
    public function testStartGameShouldCreateNewGameAndPopulateWithEvents(): void
    {
        $games = [];
        $events = [];

        $service = $this->getService($games, $events);

        $service->startGame(Uuid::uuid4(), "Home Team", "Away Team");

        $this->assertNotEmpty($games);
        $this->assertNotEmpty($events);
    }

    public function testUpdateScoreShouldAddNewScoreUpdateEvent(): void
    {
        $gameUuid = Uuid::uuid4();
        $events = [
            new StartGameEvent(),
            new ScoreUpdateEvent(0, 0),
        ];

        $games = [
            new Game($gameUuid, "Home Team", "AwayTeam", []),
        ];
        $events = [
            $gameUuid->toString() => $events,
        ];

        $service = $this->getService($games, $events);
        $this->assertCount(2, $events[$gameUuid->toString()]);
        $service->updateScore($gameUuid, 1, 0);
        $this->assertCount(3, $events[$gameUuid->toString()]);
        $lastEvent = end($events[$gameUuid->toString()]);
        $this->assertInstanceOf(ScoreUpdateEvent::class, $lastEvent);
    }

    public function testFinishGameShouldAddNewFinishGameEvent(): void
    {
        $gameUuid = Uuid::uuid4();
        $events = [
            new StartGameEvent(),
            new ScoreUpdateEvent(0, 0),
        ];

        $games = [
            new Game($gameUuid, "Home Team", "AwayTeam", []),
        ];
        $events = [
            $gameUuid->toString() => $events,
        ];

        $service = $this->getService($games, $events);
        $this->assertCount(2, $events[$gameUuid->toString()]);
        $service->finishGame($gameUuid);
        $this->assertCount(3, $events[$gameUuid->toString()]);
        $lastEvent = end($events[$gameUuid->toString()]);
        $this->assertInstanceOf(FinishGameEvent::class, $lastEvent);
    }
    private function getService(array &$games, array &$events): GameService
    {
        $gameRepositoryMock = new InMemoryGameRepository($games, $events);

        return new GameService($gameRepositoryMock);
    }
}
