<?php

namespace FwcScoreBoard\Application\DTO;

class GamesSummaryDTO
{
    /**
     * @param string[] $scores
     */
    public function __construct(
        public array $scores,
    ) {
    }
}
