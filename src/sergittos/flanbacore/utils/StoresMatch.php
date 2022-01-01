<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\utils;


use sergittos\flanbacore\match\FlanbaMatch;

trait StoresMatch {

    protected FlanbaMatch $match;

    public function getMatch(): FlanbaMatch {
        return $this->match;
    }

}