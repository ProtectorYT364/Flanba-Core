<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\item\presets\match;


use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use sergittos\flanbacore\item\FlanbaItem;
use sergittos\flanbacore\match\FlanbaMatch;
use sergittos\flanbacore\session\SessionFactory;
use sergittos\flanbacore\utils\ConfigGetter;

class EditKitItem extends FlanbaItem {

    public function __construct() {
        parent::__construct("{GOLD}Edit kit", 130);
    }

    public function onClickAir(Player $player, Vector3 $directionVector): ItemUseResult {
        $session = SessionFactory::getSession($player);
		if($session->hasMatch()){
			if($session->getMatch()->getStage() == 1){
				if($session->getMatch()->getCountdown() < 4){
					if($player->getCurrentWindow() !== null){
						$player->removeCurrentWindow();
					}
					$session->getPlayer()->sendMessage(TextFormat::RED . "Actions discarded as the game is starting!");
					return ItemUseResult::FAIL();
				}
			}
		}
		$session->sendEditKitMenu();
        return ItemUseResult::SUCCESS();
    }

}
