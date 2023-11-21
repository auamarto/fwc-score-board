<?php

namespace Application;

use FwcScoreBoard\Application\ScoreBoardProvider;
use FwcScoreBoard\Domain\Model\Event\FinishGameEvent;
use FwcScoreBoard\Domain\Model\Event\ScoreUpdateEvent;
use FwcScoreBoard\Domain\Model\Event\StartGameEvent;
use FwcScoreBoard\Domain\Model\Game;
use FwcScoreBoard\Domain\Service\GameService;
use FwcScoreBoard\Domain\Service\GameSummaryService;
use FwcScoreBoard\Infrastructure\Repository\InMemoryGameRepository;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ScoreBoardProviderTest extends TestCase
{
    public function testStartGameShouldCreateNewGameAndPopulateWithEvents(): void
    {
        $games = [];
        $events = [];

        $provider = $this->getProvider($games, $events);

        $provider->startGame("Home Team", "Away Team");

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

        $provider = $this->getProvider($games, $events);
        $this->assertCount(2, $events[$gameUuid->toString()]);
        $provider->updateScore($gameUuid, 1, 0);
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

        $provider = $this->getProvider($games, $events);
        $this->assertCount(2, $events[$gameUuid->toString()]);
        $provider->finishGame($gameUuid);
        $this->assertCount(3, $events[$gameUuid->toString()]);
        $lastEvent = end($events[$gameUuid->toString()]);
        $this->assertInstanceOf(FinishGameEvent::class, $lastEvent);
    }

    public function testGetSummaryOfGamesShouldReturnSummaryForGivenGames(): void
    {
        $mexicoCanadaUuid = Uuid::uuid4();
        $spainBrazilUuid = Uuid::uuid4();
        $germanyFranceUuid = Uuid::uuid4();
        $uruguayItalyUuid = Uuid::uuid4();
        $argentinaAustraliaUuid = Uuid::uuid4();

        $mexicoCanadaEvents = [
            new StartGameEvent(),
            new ScoreUpdateEvent(0, 0),
            new ScoreUpdateEvent(0, 5),
            new FinishGameEvent(),
        ];

        $spainBrazilEvents = [
            new StartGameEvent(),
            new ScoreUpdateEvent(0, 0),
            new ScoreUpdateEvent(10, 2),
        ];

        $germanyFranceEvents = [
            new StartGameEvent(),
            new ScoreUpdateEvent(0, 0),
            new ScoreUpdateEvent(2, 2),
        ];

        $uruguayItalyEvents = [
            new StartGameEvent(),
            new ScoreUpdateEvent(0, 0),
            new ScoreUpdateEvent(6, 6),
        ];

        $argentinaAustraliaEvents = [
            new StartGameEvent(),
            new ScoreUpdateEvent(0, 0),
            new ScoreUpdateEvent(3, 1),
        ];

        $games = [
            new Game($mexicoCanadaUuid, "Mexico", "Canada", []),
            new Game($spainBrazilUuid, "Spain", "Brazil", []),
            new Game($germanyFranceUuid, "Germany", "France", []),
            new Game($uruguayItalyUuid, "Uruguay", "Italy", []),
            new Game($argentinaAustraliaUuid, "Argentina", "Australia", []),
        ];
        $events = [
            $mexicoCanadaUuid->toString() => $mexicoCanadaEvents,
            $spainBrazilUuid->toString() => $spainBrazilEvents,
            $germanyFranceUuid->toString() => $germanyFranceEvents,
            $uruguayItalyUuid->toString() => $uruguayItalyEvents,
            $argentinaAustraliaUuid->toString() => $argentinaAustraliaEvents,
        ];
        $provider = $this->getProvider($games, $events);
        $expected = [
            "Uruguay 6 - Italy 6",
            "Spain 10 - Brazil 2",
            "Mexico 0 - Canada 5",
            "Argentina 3 - Australia 1",
            "Germany 2 - France 2",
        ];

        $actual = $provider->getSummaryOfGames();
        $this->assertEqualsCanonicalizing($expected, $actual->scores);
    }

    private function getProvider(array &$games, array &$events): ScoreBoardProvider
    {
        $gameRepository = new InMemoryGameRepository($games, $events);

        $gameService = new GameService($gameRepository);
        $gamesSummaryService = new GameSummaryService($gameRepository);


        return new ScoreBoardProvider($gameService, $gamesSummaryService);
    }
}
