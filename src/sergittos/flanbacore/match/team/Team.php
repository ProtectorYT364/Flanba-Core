<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\match\team;


use pocketmine\world\Position;
use sergittos\flanbacore\session\Session;
use sergittos\flanbacore\utils\Claim;
use sergittos\flanbacore\utils\ColorUtils;

class Team {

    private TeamSettings $settings;

    private string $name;
    private string $color;

    private int $score = 0;
    private int $kills = 0;

    /** @var Session[] */
    private array $members = [];

    public function __construct(TeamSettings $settings, string $color) {
        $this->settings = $settings;
        $this->name = strtolower(str_replace(["{", "}"], ["", ""], $color));
        $this->color = $color;
    }

    public function getSpawnPoint(): Position {
        return $this->settings->getSpawnPointPosition();
    }

    public function getWaitingPoint(): Position {
        return $this->settings->getWaitingPointPosition();
    }

    public function getArea(): Claim {
        return $this->settings->getTeamArea();
    }

    public function getGoalArea(): Claim {
        return $this->settings->getGoalArea();
    }

    public function getName(): string {
        return $this->name;
    }

    public function getColor(): string {
        return $this->color;
    }

    public function getScoreNumber(): int {
        return $this->score;
    }

    public function getScore(): string {
        if($this->color === "{RED}"){
        return match($this->score) {
            default => "",
            1 => "",
            2 => "",
            3 => "",
            4 => "",
            5 => "",
        };
        }else{
            return match($this->score) {
            default => "",
            1 => "",
            2 => "",
            3 => "",
            4 => "",
            5 => "",
        };
        }
    }

    public function getKills(): int {
        return $this->kills;
    }

    /**
     * @return Session[]
     */
    public function getMembers(): array {
        return $this->members;
    }

    public function addScore(): void {
        $this->score++;
    }

    public function addKill(): void {
        $this->kills++;
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

    public function reset(): void {
        $this->score = 0;
        $this->kills = 0;
    }

}