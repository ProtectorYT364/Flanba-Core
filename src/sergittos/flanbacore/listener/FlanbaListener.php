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
use pocketmine\event\player\PlayerExhaustEvent;
use sergittos\flanbacore\form\queue\GameSelectorForm;
use sergittos\flanbacore\form\queue\PlayForm;
use sergittos\flanbacore\session\SessionFactory;
use sergittos\flanbacore\utils\ConfigGetter;
use pocketmine\entity\Entity;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;
use sergittos\flanbacore\FlanbaCore;

class FlanbaListener implements Listener {

	public function __construct(FlanbaCore $plugin){
		$this->plugin = $plugin;
	}

	public function onJoin(PlayerJoinEvent $ev){
		$player = $ev->getPlayer();
        $ev->setJoinMessage("");
		SessionFactory::getSession($player)->setLobbyItems();

        FlanbaCore::getInstance()->getQueueManager()->getQueueByCapacity(ConfigGetter::getGamemodeMax())->addSession(
            SessionFactory::getSession($player)
        );
	}

    public function onExhaust(PlayerExhaustEvent $event){
        $event->cancel();
    }

    public function onQuit(PlayerQuitEvent $e){
        $e->setQuitMessage("");
    }

}
