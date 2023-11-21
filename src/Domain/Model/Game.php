<?php

namespace FwcScoreBoard\Domain\Model;

use FwcScoreBoard\Domain\Model\Event\ScoreUpdateEvent;
use Ramsey\Uuid\UuidInterface;

class Game
{
    /**
     * @var GameEventInterface[] $events
     */
    private readonly array $events;

    public function __construct(
        private readonly UuidInterface $id,
        private readonly string $homeTeam,
        private readonly string $awayTeam,
        array $events,
    ) {
        usort(
            $events,
            function (GameEventInterface $a, GameEventInterface $b) {
                if ($a->getTimestamp() == $b->getTimestamp()) {
                    return 0;
                }

                return ($a->getTimestamp() <= $b->getTimestamp()) ? -1 : 1;
            }
        );
        $this->events = $events;
    }

    /**
     * @return GameEventInterface[]
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getHomeTeam(): string
    {
        return $this->homeTeam;
    }

    public function getAwayTeam(): string
    {
        return $this->awayTeam;
    }

    public function getScore(): string
    {
        $scoreEvents = array_filter(
            $this->events,
            function (GameEventInterface $gameEvent) {
                return $gameEvent instanceof ScoreUpdateEvent;
            }
        );
        /** @var ScoreUpdateEvent $lastScore */
        $lastScore = end($scoreEvents);

        return sprintf(
            "%s %d - %s %d",
            $this->homeTeam,
            $lastScore->getHomeTeamScore(),
            $this->awayTeam,
            $lastScore->getAwayTeamScore()
        );
    }
}
