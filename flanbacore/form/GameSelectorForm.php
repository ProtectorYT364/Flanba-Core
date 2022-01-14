<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\form;


use EasyUI\element\Button;
use EasyUI\variant\SimpleForm;
use pocketmine\player\Player;
use sergittos\flanbacore\FlanbaCore;
use sergittos\flanbacore\session\SessionFactory;

class GameSelectorForm extends SimpleForm {

    public function __construct() {
        parent::__construct("Game selector");
    }

    protected function onCreation(): void {
        foreach(FlanbaCore::getInstance()->getQueueManager()->getQueues() as $queue) {
            $button = new Button($queue->getName());
            $button->setSubmitListener(function(Player $player) use ($queue) {
                $queue->addSession(SessionFactory::getSession($player));
            });
            $this->addButton($button);
        }
    }

}