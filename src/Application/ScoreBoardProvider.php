<?php

namespace FwcScoreBoard\Application;

use FwcScoreBoard\Application\DTO\GamesSummaryDTO;
use FwcScoreBoard\Domain\Service\GameService;
use FwcScoreBoard\Domain\Service\GameSummaryService;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ScoreBoardProvider
{
    public function __construct(
        private readonly GameService $gameService,
        private readonly GameSummaryService $gameSummaryService,
    ) {
    }

    public function startGame(string $homeTeam, string $awayTeam): void
    {
        $gameId = Uuid::uuid4();
        $this->gameService->startGame($gameId, $homeTeam, $awayTeam);
    }

    public function finishGame(UuidInterface $gameId): void
    {
        $this->gameService->finishGame($gameId);
    }

    public function updateScore(UuidInterface $gameId, int $homeTeamScore, int $awayTeamScore): void
    {
        $this->gameService->updateScore($gameId, $homeTeamScore, $awayTeamScore);
    }

    public function getSummaryOfGames(): GamesSummaryDTO
    {
        $gamesSummary = $this->gameSummaryService->getSummary();

        return new GamesSummaryDTO($gamesSummary->getFinalScores());
    }
}
