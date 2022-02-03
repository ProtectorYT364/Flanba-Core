<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\item\presets;


use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use sergittos\flanbacore\form\party\PartyForm;
use sergittos\flanbacore\item\FlanbaItem;

class PartyItem extends FlanbaItem {

    public function __construct() {
        parent::__construct("{GOLD}Party", ItemIds::NETHER_STAR);
    }

    public function onClickAir(Player $player, Vector3 $directionVector): ItemUseResult {
		return ItemUseResult::SUCCESS();
    }

}
