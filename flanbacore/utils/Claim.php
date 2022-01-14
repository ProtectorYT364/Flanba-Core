<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\utils;


use pocketmine\math\Vector3;
use pocketmine\world\Position;
use pocketmine\world\World;

class Claim {

    private Vector3 $first_vector;
    private Vector3 $second_vector;
    private World $world;

    public function __construct(Vector3 $first_vector, Vector3 $second_vector, World $world) {
        $this->first_vector = $first_vector->floor();
        $this->second_vector = $second_vector->floor();
        $this->world = $world;
    }

    static public function fromData(array $data, World $world): self {
        return new self(
            new Vector3($data["first_position"]["x"], $data["first_position"]["y"] ?? 0, $data["first_position"]["z"]),
            new Vector3($data["second_position"]["x"], $data["second_position"]["y"] ?? 0, $data["second_position"]["z"]),
            $world
        );
    }

    public function isInside(Position $position, bool $check_y = false): bool {
        $first_x = $this->first_vector->getX();
        $first_z = $this->first_vector->getZ();
        $second_x = $this->second_vector->getX();
        $second_z = $this->second_vector->getZ();

        $condition = $position->x >= min($first_x, $second_x) && $position->x <= max($first_x, $second_x) &&
            $position->z >= min($first_z, $second_z) && $position->z <= max($first_z, $second_z) &&
            $position->world->getFolderName() === $this->world->getFolderName();

        if($check_y) {
            $first_y = $this->first_vector->getY();
            $second_y = $this->second_vector->getY();

            $condition = $condition && $position->y >= min($first_y, $second_y) && $position->y <= max($first_y, $second_y);
        }
        return $condition;
    }

}