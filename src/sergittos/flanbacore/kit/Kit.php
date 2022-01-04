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
use pocketmine\item\Armor;
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
    abstract public function getArmorContents(): array;

    /**
     * @return Item[]
     */
    abstract public function getItems(): array; // TODO: Change the function name?

    public function getColor(): DyeColor {
        return $this->color;
    }

    public function setColor(DyeColor $color): void {
        foreach($this->getArmorContents() as $armor) {
            $armor->setCustomColor($color->getRgbValue());

        }
        $items = $this->getItems();
        foreach($items as $item) {
            if($item->getId() === BlockLegacyIds::TERRACOTTA) {
                // TODO: Change item color
            }
        }
        $this->color = $color;
    }

}