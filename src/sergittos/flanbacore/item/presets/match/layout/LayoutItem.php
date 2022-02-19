<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\item\presets\match\layout;


use pocketmine\utils\TextFormat;
use sergittos\flanbacore\item\FlanbaItem;
use sergittos\flanbacore\utils\ColorUtils;

class LayoutItem extends FlanbaItem {

    public function __construct(string $name, int $id, int $meta = 0) {
        $word = $this->getFirstWord($name);
        $this->setLore([
            ColorUtils::translate("{GRAY}$word your inventory layout for"),
            ColorUtils::translate("{GREEN}Flanba The Bridge."),
            ColorUtils::translate(""),
            ColorUtils::translate("{YELLOW}Click to $word!")
        ]);
        parent::__construct($name, $id, $meta);
    }

    private function getFirstWord(string $name): string {
        return TextFormat::clean(ColorUtils::translate(explode(" ", $name)[0]));
    }

}