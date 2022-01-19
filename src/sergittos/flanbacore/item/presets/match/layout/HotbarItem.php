<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\item\presets\match\layout;


use pocketmine\item\ItemIds;
use sergittos\flanbacore\item\FlanbaItem;

class HotbarItem extends FlanbaItem {

    public function __construct() {
        parent::__construct("", ItemIds::GLASS_PANE, 2);
    }

}