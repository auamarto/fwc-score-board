<?php

namespace FwcScoreBoard\Domain\Service;

use FwcScoreBoard\Domain\Model\GamesSummary;
use FwcScoreBoard\Domain\Repository\GameRepositoryInterface;

class GameSummaryService
{
    public function __construct(
        private readonly GameRepositoryInterface $gameRepository,
    ) {
    }

    public function getSummary(): GamesSummary
    {
        return $this->gameRepository->getSummary();
    }
}
