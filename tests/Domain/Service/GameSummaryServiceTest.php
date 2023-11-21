<?php

namespace Domain\Service;

use FwcScoreBoard\Domain\Model\Event\FinishGameEvent;
use FwcScoreBoard\Domain\Model\Event\ScoreUpdateEvent;
use FwcScoreBoard\Domain\Model\Event\StartGameEvent;
use FwcScoreBoard\Domain\Model\Game;
use FwcScoreBoard\Domain\Service\GameSummaryService;
use FwcScoreBoard\Infrastructure\Repository\InMemoryGameRepository;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class GameSummaryServiceTest extends TestCase
{
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
        $service = $this->getService($games, $events);
        $expected = [
            "Uruguay 6 - Italy 6",
            "Spain 10 - Brazil 2",
            "Mexico 0 - Canada 5",
            "Argentina 3 - Australia 1",
            "Germany 2 - France 2",
        ];

        $actual = $service->getSummary();
        $this->assertEqualsCanonicalizing($expected, $actual->getFinalScores());
    }

    private function getService(array &$games, array &$events): GameSummaryService
    {
        $gameRepositoryMock = new InMemoryGameRepository($games, $events);

        return new GameSummaryService($gameRepositoryMock);
    }
}
