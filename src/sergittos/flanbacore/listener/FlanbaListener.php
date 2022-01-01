<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\listener;


use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use sergittos\flanbacore\utils\ConfigGetter;

class FlanbaListener implements Listener {

    public function onFight(EntityDamageByEntityEvent $event): void {
        $event->setKnockBack(ConfigGetter::getKnockback());
        $event->setAttackCooldown(ConfigGetter::getAttackCooldown());
    }

}