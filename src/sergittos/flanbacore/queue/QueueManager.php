<?php

declare(strict_types=1);


namespace sergittos\flanbacore\queue;


use sergittos\flanbacore\map\Map;
use sergittos\flanbacore\map\MapFactory;
use sergittos\flanbacore\utils\ConfigGetter;

class QueueManager {

    /** @var Queue[] */
    private array $queues = [];

    public function __construct() {
        $this->createQueues();
    }

    /**
     * @return Queue[]
     */
    public function getQueues(): array {
        return $this->queues;
    }

    public function getQueueByCapacity(int $capacity): ?Queue {
        $queues = null;
        foreach($this->queues as $queue) {
            $queues[] = $queue;
        }

        foreach ($queues as $queue) {

            if(count($queue->getMatch()->getPlayers()) > 0 && count($queue->getMatch()->getPlayers()) <= ConfigGetter::getGamemodeMax()) {
                return $queue;
            }

        }

        return $queues[rand(0, count($queues) - 1)];
    }

    public function getQueueByCapacityAndMap(int $capacity, Map $map): ?Queue {
        foreach($this->queues as $queue) {
            if($queue->getPlayerTeamCapacity() === $capacity and $queue->getMap()->getName() === $map->getName()) {
                return $queue;
            }
        }
        return null;
    }

    private function addQueue(Queue $queue): void {
        $this->queues[] = $queue;
    }

    private function createQueues(): void {
        foreach(MapFactory::getMaps() as $map) {
            $this->addQueue(new Queue($map, ConfigGetter::getGamemodeMax()));
        }
    }

}