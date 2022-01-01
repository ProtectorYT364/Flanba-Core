<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\kit;


use pocketmine\block\utils\DyeColor;
use pocketmine\data\bedrock\DyeColorIdMap;
use pocketmine\item\VanillaItems;
use sergittos\flanbacore\match\team\Team;
use sergittos\flanbacore\utils\ColorUtils;

class TheBridgeKit extends Kit {

    private Team $team;

    public function __construct(Team $team) {
        $this->team = $team;
    }

    public function getColor(): DyeColor {
        return DyeColorIdMap::getInstance()->fromId(ColorUtils::colorToId($this->team->getColor()));
    }

    public function getArmorContents(): array {
        return [
            VanillaItems::LEATHER_CAP(),
            VanillaItems::LEATHER_TUNIC(),
            VanillaItems::LEATHER_PANTS(),
            VanillaItems::LEATHER_BOOTS()
        ];
    }

}