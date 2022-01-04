<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\session;


use pocketmine\block\utils\DyeColor;
use pocketmine\network\mcpe\protocol\ClientboundPacket;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\Server;
use sergittos\flanbacore\FlanbaCore;
use sergittos\flanbacore\item\presets\GameSelectorItem;
use sergittos\flanbacore\item\presets\LeaveMatchItem;
use sergittos\flanbacore\kit\Kit;
use sergittos\flanbacore\kit\KitFactory;
use sergittos\flanbacore\kit\TheBridgeKit;
use sergittos\flanbacore\match\FlanbaMatch;
use sergittos\flanbacore\match\team\Team;
use sergittos\flanbacore\utils\ColorUtils;
use sergittos\flanbacore\utils\ConfigGetter;
use sergittos\flanbacore\utils\scoreboard\presets\LobbyScoreboard;
use sergittos\flanbacore\utils\scoreboard\Scoreboard;

class Session {

    private Player $player;

    private FlanbaMatch|null $match = null;
    private Team|null $team = null;
    private Kit|null $kit = null;
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

    public function getKit(): ?Kit {
        return $this->kit;
    }

    public function hasKit(): bool {
        return $this->kit !== null;
    }

    public function setMatch(?FlanbaMatch $match): void {
        $this->match?->removeSession($this);
        $this->match = $match;
    }

    public function setTeam(?Team $team): void {
        $this->team?->removeMember($this);
        $this->team = $team;
    }

    private function setKit(?Kit $kit): void {
        if($kit !== null) {
            $this->clearInventory();
            $this->player->getInventory()->setContents($kit->getItems());
            $this->player->getArmorInventory()->setContents($kit->getArmorContents());
        }
        $this->kit = $kit;
    }

    public function setScoreboard(?Scoreboard $scoreboard): void {
        $this->scoreboard = $scoreboard;
        $scoreboard?->show();
    }

    public function updateScoreboard(): void {
        $this->setScoreboard($this->scoreboard);
    }

    public function setTheBridgeKit(DyeColor $color): void {
        $kit = KitFactory::getKitById(Kit::THE_BRIDGE);
        $kit->setColor($color);
        $this->setKit($kit);
    }

    public function teleportToTeamSpawnPoint(): void {
        $this->player->teleport($this->team->getWaitingPoint()); // TODO: Change the position to the spawnpoint
        $this->player->setHealth($this->player->getMaxHealth()); // TODO: Make a function for this?
        $this->setTheBridgeKit(ColorUtils::colorToDyeColor($this->getTeam()->getColor()));
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

    public function addLeaveMatchItem(): void {
        $this->clearInventory();
        $this->player->getInventory()->setItem(8, new LeaveMatchItem());
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

    public function popup(string $popup): void {
        $this->player->sendPopup(ColorUtils::translate($popup));
    }

    public function title(string $title, string $subtitle = ""): void {
        $this->player->sendTitle(ColorUtils::translate($title), ColorUtils::translate($subtitle));
    }

    public function message(string $message): void {
        $this->player->sendMessage(ColorUtils::translate($message));
    }

    public function save(): void {
        FlanbaCore::getInstance()->getProvider()->saveSession($this);
    }

}