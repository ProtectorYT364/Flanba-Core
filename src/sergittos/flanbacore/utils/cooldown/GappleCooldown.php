<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\utils\cooldown;


use sergittos\flanbacore\utils\ConfigGetter;

class GappleCooldown extends Cooldown {

    public function __construct() {
        parent::__construct(self::GAPPLE, ConfigGetter::getGappleCooldownSeconds());
    }

}