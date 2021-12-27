<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace flanbacore\item\presets;


use flanbacore\form\queue\UnrankedQueueForm;
use flanbacore\item\FlanbaItem;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class UnrankedQueueItem extends FlanbaItem {

    public function __construct() {
        parent::__construct("{GOLD}Unranked queue", ItemIds::IRON_SWORD);
    }

    // TODO: Add InvMenu form instead simple form

    public function onClickAir(Player $player, Vector3 $directionVector): ItemUseResult {
        $player->sendForm(new UnrankedQueueForm());
        return ItemUseResult::SUCCESS();
    }

}