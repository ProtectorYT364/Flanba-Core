<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\form;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use sergittos\flanbacore\FlanbaCore;
use sergittos\flanbacore\match\FlanbaMatch;
use sergittos\flanbacore\session\SessionFactory;

class SpectateMatchForm extends \jojoe77777\FormAPI\SimpleForm {

	private Player $player1;

	public function __construct(Player $player1) {
		$this->player1 = $player1;
		parent::__construct(function(Player $player, $data = null) : void{
			if($data == null) return;
			foreach(FlanbaCore::getInstance()->getMatchManager()->getMatchFromId($data)->getArena()->getWorld()->getPlayers() as $players){
				$players->sendMessage(TextFormat::LIGHT_PURPLE . $this->player1->getName() . TextFormat::YELLOW . " started spectating");
			}
			$this->player1->teleport(FlanbaCore::getInstance()->getMatchManager()->getMatchFromId($data)->getPlayers()[0]->getPlayer()->getPosition());
			$this->player1->setGamemode(GameMode::SPECTATOR());

			$this->player1->sendMessage(TextFormat::GREEN . "You just started spectating the match, to leave, do /hub or use the bed in your inventory..");
            $session = SessionFactory::getSession($this->player1);

            $inventory = $this->player1->getInventory();

            $inventory->clearAll();
            $session->setSpectatorItems();
;
		});
		$this->setTitle("Spectate a Match");
		foreach(FlanbaCore::getInstance()->getMatchManager()->getMatches() as $match){

            if(!is_null($match)) {

                if ($match->getPlayerTeamCapacity() == 1) {

                    $players = $match->getPlayers();
                    if(array_key_exists(0, $players) && array_key_exists(1, $players))
                    $this->addButton($players[0]->getPlayer()->getName() . " vs " . $players[1]->getPlayer()->getName(), -1, "", $match->getId());
                } elseif ($match->getPlayerTeamCapacity() == 2) {

                    $players = $match->getPlayers();

                    if(array_key_exists(0, $players) && array_key_exists(1, $players) && array_key_exists(2, $players) && array_key_exists(3, $players))
                    $this->addButton($players[0]->getPlayer()->getName() . ", " . $players[1]->getPlayer()->getName() . " vs " . $players[2]->getPlayer()->getName() . ", " . $players[3]->getPlayer()->getName(), -1, "", $match->getId());

                } elseif ($match->getPlayerTeamCapacity() == 3) {

                    $players = $match->getPlayers();
                    if(array_key_exists(0, $players))
                        $this->addButton($players[0]->getPlayer()->getName() . " (+5, 3v3)", -1, "", $match->getId());

                } elseif ($match->getPlayerTeamCapacity() == 4) {

                    $players = $match->getPlayers();
                    if(array_key_exists(0, $players))
                    $this->addButton($players[0]->getPlayer()->getName() . " (+7, 4v4)", -1, "", $match->getId());

                }
            }
        }
    }
}
