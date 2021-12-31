<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\session;


use pocketmine\network\mcpe\protocol\ClientboundPacket;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\Server;
use sergittos\flanbacore\FlanbaCore;
use sergittos\flanbacore\item\presets\GameSelectorItem;
use sergittos\flanbacore\match\FlanbaMatch;
use sergittos\flanbacore\match\Team;
use sergittos\flanbacore\utils\ColorUtils;
use sergittos\flanbacore\utils\ConfigGetter;
use sergittos\flanbacore\utils\scoreboard\presets\LobbyScoreboard;
use sergittos\flanbacore\utils\scoreboard\Scoreboard;

class Session {

    private Player $player;

    private FlanbaMatch|null $match = null;
    private Team|null $team = null;
    private Scoreboard|null $scoreboard = null;

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

    public function getScoreboard(): ?Scoreboard {
        return $this->scoreboard;
    }

    public function hasScoreboard(): bool {
        return $this->scoreboard !== null;
    }

    public function setMatch(?FlanbaMatch $match): void {
        $this->match?->removeSession($this);
        $this->match = $match;
    }

    public function setTeam(?Team $team): void {
        $this->team = $team;
    }

    public function setScoreboard(?Scoreboard $scoreboard): void {
        $this->scoreboard = $scoreboard;
        $scoreboard?->show();
    }

    public function teleportToLobby(): void {
        $hunger_manager = $this->player->getHungerManager();
        $hunger_manager->setFood($hunger_manager->getMaxFood());
        $this->player->setHealth($this->player->getMaxHealth());
        $this->player->getEffects()->clear();
        $this->player->setGamemode(GameMode::SURVIVAL());
        $this->setLobbyItems();
        $this->setScoreboard(new LobbyScoreboard($this));

        $this->player->teleport(Server::getInstance()->getWorldManager()->getWorldByName(ConfigGetter::getLobbyWorldName())->getSafeSpawn());
    }

    private function setLobbyItems(): void {
        $this->clearInventory();

        $inventory = $this->player->getInventory();
        $inventory->setItem(4, new GameSelectorItem());
    }

    private function clearInventory(): void {
        $this->player->getInventory()->clearAll();
        $this->player->getArmorInventory()->clearAll();
    }

    public function setImmobile(bool $immobile = true): void {
        $this->player->setImmobile($immobile);
    }

    public function sendDataPacket(ClientboundPacket $packet): void {
        $this->player->getNetworkSession()->sendDataPacket($packet);
    }

    public function getPing(): int {
        return $this->player->getNetworkSession()->getPing();
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