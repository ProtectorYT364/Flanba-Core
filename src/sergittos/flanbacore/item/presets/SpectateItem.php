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
use sergittos\flanbacore\form\SpectateMatchForm;
use sergittos\flanbacore\item\FlanbaItem;

class SpectateItem extends FlanbaItem {

    public function __construct() {
        parent::__construct("{GOLD}Spectate", ItemIds::BANNER_PATTERN);
    }

    public function onClickAir(Player $player, Vector3 $directionVector): ItemUseResult {
        $player->sendForm(new SpectateMatchForm($player));
        return ItemUseResult::SUCCESS();
    }

}