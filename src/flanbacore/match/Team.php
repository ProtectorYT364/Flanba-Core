<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace flanbacore\match;


use flanbacore\session\Session;

class Team {

    private FlanbaMatch $match;
    private string $color;

    /** @var Session[] */
    private array $members;

    /**
     * @param FlanbaMatch $match
     * @param string $color
     * @param Session[] $members
     */
    public function __construct(FlanbaMatch $match, string $color, array $members) {
        $this->match = $match;
        $this->color = $color;
        $this->members = $members;
    }

    public function getMatch(): FlanbaMatch {
        return $this->match;
    }

    public function getColor(): string {
        return $this->color;
    }

    /**
     * @return Session[]
     */
    public function getMembers(): array {
        return $this->members;
    }

}