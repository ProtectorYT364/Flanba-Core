<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace flanbacore\arena;


use flanbacore\utils\claim\Claim;
use pocketmine\Server;
use pocketmine\world\Position;
use pocketmine\world\World;

class Arena {

    private string $id;

    private World $world;
    private Claim $claim;

    private array $positions;

    /**
     * @param string $id
     * @param World $world
     * @param Claim $claim
     * @param Position[] $positions
     */
    public function __construct(string $id, World $world, Claim $claim, array $positions) {
        $this->id = $id;
        $this->world = $world;
        $this->claim = $claim;
        $this->positions = $positions;
    }

    public function getId(): string {
        return $this->id;
    }

    public function getWorld(): World {
        return $this->world;
    }

    public function getClaim(): Claim {
        return $this->claim;
    }

    /**
     * @return Position[]
     */
    public function getPositions(): array {
        return $this->positions;
    }

    public function reset(): void {
        $positions = $this->positions;
        $world_name = $this->world->getFolderName();

        $world_manager = Server::getInstance()->getWorldManager();
        $world_manager->unloadWorld($this->world);
        $world_manager->loadWorld($world_name, true);

        $this->world = $world_manager->getWorldByName($world_name);
        $this->world->setTime(World::TIME_DAY);
        $this->world->stopTime();
        $this->world->setAutoSave(false);

        foreach($positions as $position) {
            $position->world = $this->world;
        }
    }

}