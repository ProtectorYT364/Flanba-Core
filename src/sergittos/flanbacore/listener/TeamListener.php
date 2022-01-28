<?php

declare(strict_types=1);


namespace sergittos\flanbacore\listener;


use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use sergittos\flanbacore\session\SessionFactory;

class TeamListener implements Listener {

    public function onFight(EntityDamageByEntityEvent $event): void {
        $entity = $event->getEntity();
        $damager = $event->getDamager();
        if($entity instanceof Player and $damager instanceof Player) {
            $entity_session = SessionFactory::getSession($entity);
            $damager_session = SessionFactory::getSession($damager);
            if($entity_session->hasMatch() and $damager_session->hasMatch() and
                $entity_session->getTeam()->getColor() === $damager_session->getTeam()->getColor() and
                $entity_session->getMatch()->getId() === $damager_session->getMatch()->getId()) {
                $event->cancel();
            }
        }
    }

}