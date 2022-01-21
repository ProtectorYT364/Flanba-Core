<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\form;

use pocketmine\player\Player;
use sergittos\flanbacore\FlanbaCore;
use sergittos\flanbacore\session\SessionFactory;

class SpectateMatchForm extends \jojoe77777\FormAPI\SimpleForm {

    public function __construct(Player $player) {
		parent::__construct(function(Player $player, $data = null) : void{
			$player->teleport(FlanbaCore::getInstance()->getServer()->getPlayerExact($data)->getPosition());
		});
		$this->setTitle("Spectate Match");
		foreach(FlanbaCore::getInstance()->getServer()->getOnlinePlayers() as $players){
			$session = SessionFactory::getSession($players);
			if($session->hasMatch()){
				$this->addButton($session->getPlayer()->getName());
			}
		}
		$player->sendForm($this);

    }
}