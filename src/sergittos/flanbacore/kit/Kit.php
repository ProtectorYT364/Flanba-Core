<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\kit;


use pocketmine\block\utils\DyeColor;
use pocketmine\item\Armor;

abstract class Kit {

    abstract public function getColor(): DyeColor;

    /**
     * @return Armor[]
     */
    abstract public function getArmorContents(): array;



}