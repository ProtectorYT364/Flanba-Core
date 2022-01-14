<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\match\team;


use pocketmine\world\Position;
use pocketmine\world\World;
use sergittos\flanbacore\utils\Claim;

class TeamSettings {

    private Position $waiting_point_position;
    private Position $spawn_point_position;

    private Claim $team_area;
    private Claim $goal_area;

    public function __construct(Position $waiting_point_position, Position $spawn_point_position, Claim $team_area, Claim $goal_area) {
        $this->waiting_point_position = $waiting_point_position;
        $this->spawn_point_position = $spawn_point_position;
        $this->team_area = $team_area;
        $this->goal_area = $goal_area;
    }

    static public function fromData(array $data, World $world): self {
        return new self(
            new Position($data["waiting_point"]["x"], $data["waiting_point"]["y"], $data["waiting_point"]["z"], $world),
            new Position($data["spawn_point"]["x"], $data["spawn_point"]["y"], $data["spawn_point"]["z"], $world),
            Claim::fromData($data["safe_zone"], $world), Claim::fromData($data["goal_area"], $world)
        );
    }

    public function getWaitingPointPosition(): Position {
        return $this->waiting_point_position;
    }

    public function getSpawnPointPosition(): Position {
        return $this->spawn_point_position;
    }

    public function getTeamArea(): Claim {
        return $this->team_area;
    }

    public function getGoalArea(): Claim {
        return $this->goal_area;
    }

    public function updatePositionsWorld(World $world): void {
        $this->waiting_point_position->world = $world;
        $this->spawn_point_position->world = $world;
    }

}