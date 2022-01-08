<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\listener;


use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacketV1;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacketV2;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;

class ItemListener implements Listener {

    public function onTransaction(InventoryTransactionEvent $event): void {
        foreach($event->getTransaction()->getActions() as $action) {
            if($this->hasFlanbaTag($action->getSourceItem()) !== null) {
                $event->cancel();
            }
        }
    }

    public function onItemUse(PlayerItemUseEvent $event): void {
        if($this->hasFlanbaTag($event->getItem())) {
            $player = $event->getPlayer();
            $packet = new LevelSoundEventPacket();
            $packet->sound = LevelSoundEvent::BUBBLE_POP;
            $packet->position = $player->getPosition();
            $player->getNetworkSession()->sendDataPacket($packet);
        }
    }

    private function hasFlanbaTag(Item $item): bool {
        return $item->getNamedTag()->getTag("flanba") !== null;
    }

}