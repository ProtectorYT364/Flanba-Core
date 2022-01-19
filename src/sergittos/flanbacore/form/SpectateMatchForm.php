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
use sergittos\flanbacore\match\FlanbaMatch;
use sergittos\flanbacore\session\SessionFactory;
use sergittos\flanbacore\utils\ColorUtils;

class SpectateMatchForm extends SimpleForm {

    public function __construct() {
        parent::__construct("Spectate a match", "Select the match that you want to spectate");
    }

    protected function onCreation(): void {
        foreach(FlanbaCore::getInstance()->getMatchManager()->getMatches() as $match) {
            if(!$this->checkStage($match)) {
                continue;
            }
            $button = new Button($match->getId()); // TODO: Change this to a name or idk
            $button->setSubmitListener(function(Player $player) use ($match) {
                if($this->checkStage($match)) {
                    $player->sendMessage(ColorUtils::translate("{RED}You can't spectate this match because it has finished."));
                } else {
                    $match->addSpectator(SessionFactory::getSession($player));
                }
            });
        }
        if(empty($this->getButtons())) {
            $this->setHeaderText("Seems look like there is no matches to spectate.");
        }
    }

    private function checkStage(FlanbaMatch $match): bool {
        $stage = $match->getStage();
        return $stage !== FlanbaMatch::WAITING_STAGE and
        $stage !== FlanbaMatch::COUNTDOWN_STAGE and
        $stage !== FlanbaMatch::ENDING_STAGE;
    }

}