<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace flanbacore\queue;


class QueueManager {

    /** @var Queue[] */
    private array $queues = [];

    public function __construct() {
        $this->addQueue(new TheBridgeQueue());
    }

    /**
     * @return Queue[]
     */
    public function getQueues(): array {
        return $this->queues;
    }

    private function addQueue(Queue $queue): void {
        $this->queues[$queue->getId()] = $queue;
    }

}