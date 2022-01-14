<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore;


use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\scheduler\Task;
use pocketmine\world\Position;
use pocketmine\world\World;

class TestTask extends Task {

    private World $world;

    /** @var Position[] */
    private array $positions;

    public function __construct(Position $position) {
        $this->world = $position->world;
        $this->positions = [
            $position,

            $position->add(1, 0, 0),
            $position->add(2, 0, 0),
            $position->add(3, 0, 0),
            $position->subtract(1, 0, 0),
            $position->subtract(2, 0, 0),
            $position->subtract(3, 0, 0),

            $position->add(0, 0, 1),
            $position->add(0, 0, 2),
            $position->add(0, 0, 3),
            $position->subtract(0, 0, 1),
            $position->subtract(0, 0, 2),
            $position->subtract(0, 0, 3),

            $position->add(1, 0, 1),
            $position->add(1, 0, 2),
            $position->add(1, 0, 3),
            $position->add(2, 0, 1),
            $position->add(2, 0, 2),
            $position->add(2, 0, 3),
            $position->add(3, 0, 1),
            $position->add(3, 0, 2),
            $position->add(3, 0, 3),

            $position->add(1, 0, 0)->subtract(0, 0, 1),
            $position->add(1, 0, 0)->subtract(0, 0, 2),
            $position->add(1, 0, 0)->subtract(0, 0, 3),
            $position->add(2, 0, 0)->subtract(0, 0, 1),
            $position->add(2, 0, 0)->subtract(0, 0, 2),
            $position->add(2, 0, 0)->subtract(0, 0, 3),
            $position->add(3, 0, 0)->subtract(0, 0, 1),
            $position->add(3, 0, 0)->subtract(0, 0, 2),
            $position->add(3, 0, 0)->subtract(0, 0, 3),

            $position->subtract(1, 0, 1),
            $position->subtract(2, 0, 1),
            $position->subtract(3, 0, 1),
            $position->subtract(1, 0, 2),
            $position->subtract(2, 0, 2),
            $position->subtract(3, 0, 2),
            $position->subtract(1, 0, 3),
            $position->subtract(2, 0, 3),
            $position->subtract(3, 0, 3),

            $position->subtract(1, 0, 0)->add(0, 0, 1),
            $position->subtract(1, 0, 0)->add(0, 0, 2),
            $position->subtract(1, 0, 0)->add(0, 0, 3),
            $position->subtract(2, 0, 0)->add(0, 0, 1),
            $position->subtract(2, 0, 0)->add(0, 0, 2),
            $position->subtract(2, 0, 0)->add(0, 0, 3),
            $position->subtract(3, 0, 0)->add(0, 0, 1),
            $position->subtract(3, 0, 0)->add(0, 0, 2),
            $position->subtract(3, 0, 0)->add(0, 0, 3),
        ];
    }

    public function onRun(): void {
        $position = array_shift($this->positions);
        if($position === null) {
            $this->getHandler()->cancel();
            return;
        }
        $colors = DyeColor::getAll();
        $color = $colors[array_rand($colors)];
        $block = VanillaBlocks::WOOL()->setColor($color);

        try {
            $this->world->setBlock($position, $block);
        } catch(\InvalidArgumentException $exception) {}
    }

}