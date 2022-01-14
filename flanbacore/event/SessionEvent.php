<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\event;


use pocketmine\event\Event;
use sergittos\flanbacore\session\Session;

class SessionEvent extends Event {

    private Session $session;

    public function __construct(Session $session) {
        $this->session = $session;
    }

    public function getSession(): Session {
        return $this->session;
    }

}