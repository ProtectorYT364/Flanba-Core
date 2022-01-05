<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\kit;


use pocketmine\block\BlockLegacyIds;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\DyeColorIdMap;
use pocketmine\item\Armor;
use pocketmine\item\Durable;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;

abstract class Kit {

    public const THE_BRIDGE = 0;

    private DyeColor $color;

    abstract public function getId(): int;

    /**
     * @return Armor[]
     */
    abstract public function getArmorContents(DyeColor $color): array;

    /**
     * @return Item[]
     */
    abstract public function getItems(DyeColor $color): array; // TODO: Change the function name?

    public function getColor(): DyeColor {
        return $this->color;
    }

}