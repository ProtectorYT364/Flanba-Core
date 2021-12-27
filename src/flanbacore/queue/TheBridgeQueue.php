<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace flanbacore\queue;


class TheBridgeQueue extends Queue {

    public function __construct() {
        parent::__construct(self::THE_BRIDGE, "The Bridge");
    }

}