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
use sergittos\flanbacore\map\MapFactory;

class SelectMapForm extends SimpleForm {

    public function __construct() {
        parent::__construct("Select a map", "What map do you want to select?");
    }

    protected function onCreation(): void {
        foreach(MapFactory::getMaps() as $map) {
            $button = new Button($map->getName());
            $button->setSubmitListener(function(Player $player) {
                // TODO
            });
            $this->addButton($button);
        }
    }

}