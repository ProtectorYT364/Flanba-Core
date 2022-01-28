<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\form\queue;


use EasyUI\element\Button;
use EasyUI\variant\SimpleForm;
use pocketmine\player\Player;
use sergittos\flanbacore\FlanbaCore;
use sergittos\flanbacore\map\MapFactory;
use sergittos\flanbacore\session\SessionFactory;

class SelectMapForm extends SimpleForm {

    private int $player_team_capacity;

    public function __construct(int $player_team_capacity) {
        $this->player_team_capacity = $player_team_capacity;
        parent::__construct("Select a map", "What map do you want to select?");
    }

    protected function onCreation(): void {
        foreach(MapFactory::getMaps() as $map) {
            $button = new Button($map->getName());
            $button->setSubmitListener(function(Player $player) use ($map) {
                FlanbaCore::getInstance()->getQueueManager()->getQueueByCapacityAndMap($this->player_team_capacity, $map)->addSession(
                    SessionFactory::getSession($player)
                );
            });
            $this->addButton($button);
        }
    }

}