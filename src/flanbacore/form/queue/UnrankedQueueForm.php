<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace flanbacore\form\queue;


use EasyUI\element\Button;
use EasyUI\variant\SimpleForm;
use flanbacore\FlanbaCore;
use flanbacore\session\SessionFactory;
use pocketmine\player\Player;

class UnrankedQueueForm extends SimpleForm {

    public function __construct() {
        parent::__construct("Unranked queue");
    }

    protected function onCreation(): void {
        foreach(FlanbaCore::getInstance()->getQueueManager()->getQueues() as $queue) {
            $button = new Button($queue->getName() . "\nQueued players: " . $queue->getPlayersCount());
            $button->setSubmitListener(function(Player $player) use ($queue) {
                $queue->addSession(SessionFactory::getSession($player));
            });
            $this->addButton($button);
        }
    }

}