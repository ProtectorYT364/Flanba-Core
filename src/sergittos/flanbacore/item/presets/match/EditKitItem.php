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
use sergittos\flanbacore\item\FlanbaItem;
use sergittos\flanbacore\session\SessionFactory;

class EditKitItem extends FlanbaItem {

    public function __construct() {
        parent::__construct("{GOLD}Edit kit", ItemIds::ANVIL);
    }

    public function onClickAir(Player $player, Vector3 $directionVector): ItemUseResult {
        SessionFactory::getSession($player)->sendEditKitMenu();
        return ItemUseResult::SUCCESS();
    }

}