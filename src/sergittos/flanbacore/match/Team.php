<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\match;


use pocketmine\world\Position;
use sergittos\flanbacore\session\Session;
use sergittos\flanbacore\utils\ColorUtils;

class Team {

    private FlanbaMatch $match;
    private Position $spawn_point;
    private string $color;

    /** @var Session[] */
    private array $members;

    public function __construct(FlanbaMatch $match, Position $spawn_point, string $color) {
        $this->match = $match;
        $this->spawn_point = $spawn_point;
        $this->color = $color;
    }

    public function getMatch(): FlanbaMatch {
        return $this->match;
    }

    public function getSpawnPoint(): Position {
        return $this->spawn_point;
    }

    public function getColor(): string {
        return ColorUtils::translate($this->color . ColorUtils::colorToString($this->color));
    }

    /**
     * @return Session[]
     */
    public function getMembers(): array {
        return $this->members;
    }

    public function hasMember(Session $member): bool {
        return in_array($member,  $this->members, true);
    }

    public function addMember(Session $member): void {
        $this->members[] = $member;
    }

    public function removeMember(Session $member): void {
        unset($this->members[array_search($member, $this->members, true)]);
    }

}