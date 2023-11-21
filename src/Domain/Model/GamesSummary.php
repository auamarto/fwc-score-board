<?php

namespace FwcScoreBoard\Domain\Model;

class GamesSummary
{
    /**
     * @param Game[] $games
     */
    public function __construct(
        private readonly array $games,
    ) {
    }

    /**
     * @return string[]
     */
    public function getFinalScores(): array
    {
        return array_map(
            function (Game $game) {
                return $game->getScore();
            },
            $this->games
        );
    }
}
