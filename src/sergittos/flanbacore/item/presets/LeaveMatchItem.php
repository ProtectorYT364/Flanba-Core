<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\item\presets;


use pocketmine\block\utils\DyeColor;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use sergittos\flanbacore\item\FlanbaItem;
use sergittos\flanbacore\session\SessionFactory;

class LeaveMatchItem extends FlanbaItem {

    public function __construct() {
        parent::__construct("{RED}Leave match", ItemIds::BED, DyeColor::RED()->id());
    }

    public function onClickAir(Player $player, Vector3 $directionVector): ItemUseResult {
        $session = SessionFactory::getSession($player);
		if($session->getPlayer()->getGamemode() === GameMode::SPECTATOR()){
			$session->teleportToLobby();
			return ItemUseResult::SUCCESS();
		}
        if(!$session->hasMatch()) {
            $session->message("{RED}You must be on a match to do this!");
            return ItemUseResult::FAIL();
        }
        $session->setMatch(null, false);
        return ItemUseResult::SUCCESS();
    }

}