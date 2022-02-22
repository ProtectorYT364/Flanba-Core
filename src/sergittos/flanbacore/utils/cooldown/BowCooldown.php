<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\utils\cooldown;


use pocketmine\item\VanillaItems;
use sergittos\flanbacore\utils\ConfigGetter;

class BowCooldown extends Cooldown {

    public function __construct() {
        parent::__construct(self::BOW, ConfigGetter::getBowCooldownSeconds());
    }

    public function onRun(): bool {
        /*
         * 5 - 1
         * 4 - 2
         * 3 - 3
         * 2 - 4
         * 1 - 5
         */
        // TODO: Add sound
        $on_run = parent::onRun();
        if($on_run) {
			$player = $this->session->getPlayer();
			if($player->isConnected()) {
				if($player->getInventory()->isSlotEmpty($this->session->getKit()->getLayout()->getArrowSlot())){
					$player->getInventory()->setItem(
						$this->session->getKit()->getLayout()->getArrowSlot(), VanillaItems::ARROW()
					);
				} else {
					$player->getInventory()->addItem(
						VanillaItems::ARROW()
					);
				}
			}
        }
        return $on_run;
    }

}