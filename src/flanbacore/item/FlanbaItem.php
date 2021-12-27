<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace flanbacore\item;


use flanbacore\utils\ColorUtils;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;

class FlanbaItem extends Item {

    public function __construct(string $name, int $id) {
        $this->setCustomName($name = ColorUtils::translate($name));
        parent::__construct(new ItemIdentifier($id, 0), $name);
        $this->getNamedTag()->setString("flanba", "flanba");
    }

}