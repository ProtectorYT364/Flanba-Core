<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace flanbacore\session;


use flanbacore\FlanbaCore;
use flanbacore\match\FlanbaMatch;
use flanbacore\queue\Queue;
use flanbacore\utils\ColorUtils;
use pocketmine\network\mcpe\protocol\ClientboundPacket;
use pocketmine\player\Player;

class Session {

    private Player $player;

    private FlanbaMatch|null $match = null;
    private Queue|null $queue = null;

    public function __construct(Player $player) {
        $this->player = $player;
    }

    public function getPlayer(): Player {
        return $this->player;
    }

    public function getMatch(): ?FlanbaMatch {
        return $this->match;
    }

    public function hasMatch(): bool {
        return $this->match !== null;
    }

    public function getQueue(): ?Queue {
        return $this->queue;
    }

    public function hasQueue(): bool {
        return $this->queue !== null;
    }

    public function setMatch(?FlanbaMatch $match): void {
        $this->match?->removeSession($this);
        $this->match = $match;
    }

    public function setQueue(?Queue $queue): void {
        $this->queue?->removeSession($this);
        $this->queue = $queue;
    }

    public function sendDataPacket(ClientboundPacket $packet): void {
        $this->player->getNetworkSession()->sendDataPacket($packet);
    }

    public function getUsername(): string {
        return $this->player->getName();
    }

    public function popup(string $text): void {
        $this->player->sendPopup(ColorUtils::translate($text));
    }

    public function message(string $text): void {
        $this->player->sendMessage(ColorUtils::translate($text));
    }

    public function save(): void {
        FlanbaCore::getInstance()->getProvider()->saveSession($this);
    }

}