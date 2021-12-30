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
use flanbacore\match\Team;
use flanbacore\queue\Queue;
use flanbacore\utils\ColorUtils;
use pocketmine\network\mcpe\protocol\ClientboundPacket;
use pocketmine\player\Player;

class Session {

    private Player $player;

    private FlanbaMatch|null $match = null;
    private Team|null $team = null;

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

    public function getTeam(): ?Team {
        return $this->team;
    }

    public function hasTeam(): bool {
        return $this->team !== null;
    }

    public function setMatch(?FlanbaMatch $match): void {
        $this->match?->removeSession($this);
        $this->match = $match;
    }

    public function setTeam(?Team $team): void {
        $this->team = $team;
    }

    public function setImmobile(bool $immobile = true): void {
        $this->player->setImmobile($immobile);
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