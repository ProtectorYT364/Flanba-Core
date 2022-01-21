<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\form;

use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use sergittos\flanbacore\FlanbaCore;
use sergittos\flanbacore\item\presets\match\LeaveMatchItem;
use sergittos\flanbacore\session\SessionFactory;

class SpectateMatchForm extends \jojoe77777\FormAPI\SimpleForm {

	private Player $player1;

	public function __construct(Player $player1) {
		$this->player1 = $player1;
		parent::__construct(function(Player $player, $data = null) : void{
			if($data == null) return;
			$this->player1->teleport(FlanbaCore::getInstance()->getServer()->getPlayerExact($data)->getPosition());
			$this->player1->setGamemode(GameMode::SPECTATOR());
			$this->player1->sendMessage(TextFormat::GREEN . "You just started spectating " . TextFormat::AQUA . $data . "," . TextFormat::GREEN . " To leave, do /hub.");
			$inventory = $this->player1->getInventory();

			$inventory->clearAll();
			//$inventory->setItem(7, new LeaveMatchItem()); //for now ill disable it, as its useless
		});
		$this->setTitle("Spectate a Match");
		foreach(FlanbaCore::getInstance()->getServer()->getOnlinePlayers() as $players){
			$session = SessionFactory::getSession($players);
			if($session->hasMatch()){
				$this->addButton($session->getPlayer()->getName(), -1, "", $session->getPlayer()->getName());
			}
		}
    }
}