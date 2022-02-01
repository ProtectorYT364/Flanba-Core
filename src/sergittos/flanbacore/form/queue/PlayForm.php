<?php

declare(strict_types=1);


namespace sergittos\flanbacore\form\queue;


use EasyUI\element\Button;
use EasyUI\variant\SimpleForm;
use pocketmine\player\Player;
use sergittos\flanbacore\FlanbaCore;
use sergittos\flanbacore\session\SessionFactory;

class PlayForm extends SimpleForm {

    private int $player_team_capacity;

    public function __construct(int $player_team_capacity) {
        $this->player_team_capacity = $player_team_capacity;
        parent::__construct("Play " . (
            $player_team_capacity === 1 ? "solo" :
                ($player_team_capacity === 2 ? "duos" :
                    ($player_team_capacity === 3 ? "trios" : "squads")
                )
            )
        );
    }

    protected function onCreation(): void {
        $this->addRandomMapButton();
        $this->addRedirectFormButton("Select map", new SelectMapForm($this->player_team_capacity));
    }

    private function addRandomMapButton(): void {
        $button = new Button("Random Map");
        $button->setSubmitListener(function(Player $player) {
            FlanbaCore::getInstance()->getQueueManager()->getQueueByCapacity($this->player_team_capacity)->addSession(
                SessionFactory::getSession($player)
            );
        });
        $this->addButton($button);
    }

}