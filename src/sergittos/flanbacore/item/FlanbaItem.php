<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\item;


use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use sergittos\flanbacore\utils\ColorUtils;

class FlanbaItem extends Item {

    public function __construct(string $name, int $id, int $meta = 0) {
        $this->setCustomName($name = ColorUtils::translate($name));
        parent::__construct(new ItemIdentifier($id, $meta), $name);
        $this->getNamedTag()->setString("flanba", "flanba");
    }

}