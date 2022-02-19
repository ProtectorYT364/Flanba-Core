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

class ResetLayoutItem extends LayoutItem {

    public function __construct() {
        parent::__construct("{RED}Reset layout\n§7Reset your inventory layout for\n§eFlanba §aBridge\n\n§eClick to reset!", ItemIds::BARRIER);
    }

}
