<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\form\queue\mode;


use EasyUI\element\Button;
use EasyUI\variant\SimpleForm;
use pocketmine\player\Player;
use sergittos\flanbacore\FlanbaCore;
use sergittos\flanbacore\form\queue\SelectMapForm;
use sergittos\flanbacore\session\SessionFactory;

class ModeForm extends SimpleForm {

    public function __construct(string $title) {
        parent::__construct($title);
    }

    protected function onCreation(): void {
        $this->addRandomMapButton(); // TODO: Do solos, duos and squads
        $this->addRedirectFormButton("Select map", new SelectMapForm());
    }

    private function addRandomMapButton(): void {
        foreach(FlanbaCore::getInstance()->getQueueManager()->getQueues() as $queue) {
            $button = new Button($queue->getName());
            $button->setSubmitListener(function(Player $player) use ($queue) {
                $queue->addSession(SessionFactory::getSession($player));
            });
            $this->addButton($button);
        }
    }

}