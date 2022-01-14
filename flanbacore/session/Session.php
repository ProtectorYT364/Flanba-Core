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
use sergittos\flanbacore\item\presets\ProfileItem;
use sergittos\flanbacore\item\presets\SettingsItem;
use sergittos\flanbacore\item\presets\ShopItem;
use sergittos\flanbacore\item\presets\SpectateItem;
use sergittos\flanbacore\item\presets\PartyItem;
use sergittos\flanbacore\item\presets\UnlockedItem;
use sergittos\flanbacore\kit\Kit;
use sergittos\flanbacore\kit\KitFactory;
use sergittos\flanbacore\match\FlanbaMatch;
use sergittos\flanbacore\match\team\Team;
use sergittos\flanbacore\utils\ColorUtils;
use sergittos\flanbacore\utils\ConfigGetter;
use sergittos\flanbacore\utils\cooldown\Cooldown;
use sergittos\flanbacore\utils\scoreboard\presets\LobbyScoreboard;
use sergittos\flanbacore\utils\scoreboard\Scoreboard;

class Session {

    private Player $player;

    private FlanbaMatch|null $match = null;
    private Team|null $team = null;
    private Kit|null $kit = null;
    private Scoreboard|null $scoreboard = null;

    /** @var Cooldown[] */
    private array $cooldowns = [];

    public function __construct(Player $player) {
        $this->player = $player;
    }

    public function getPlayer(): Player {
        return $this->player;
    }

    public function getMatch(): ?FlanbaMatch {
        return $this->match;
    }

    public function getTeam(): ?Team {
        return $this->team;
    }

    public function getKit(): ?Kit {
        return $this->kit;
    }

    public function getScoreboard(): ?Scoreboard {
        return $this->scoreboard;
    }

    /**
     * @return Cooldown[]
     */
    public function getCooldowns(): array {
        return $this->cooldowns;
    }

    public function getCooldownById(string $id): ?Cooldown {
        return $this->cooldowns[$id] ?? null;
    }

    public function hasMatch(): bool {
        return $this->match !== null;
    }

    public function hasTeam(): bool {
        return $this->team !== null;
    }

    public function hasKit(): bool {
        return $this->kit !== null;
    }

    public function hasCooldown(string $id): bool {
        return array_key_exists($id, $this->cooldowns);
    }

    public function setMatch(?FlanbaMatch $match, bool $finish): void {
        $this->match?->removeSession($this, $finish); // TODO: Fix this
        $this->match = $match;
    }

    public function setTeam(?Team $team): void {
        $this->team?->removeMember($this);
        $this->team = $team;
    }

    private function setKit(?Kit $kit, DyeColor $color): void {
        if($kit !== null) {
            $this->clearInventory();
            $this->player->getInventory()->setContents($kit->getItems($color));
            $this->player->getArmorInventory()->setContents($kit->getArmorContents($color));
        }
        $this->kit = $kit;
    }

    public function setTheBridgeKit(DyeColor $color): void {
        $this->setKit(KitFactory::getKitById(Kit::THE_BRIDGE), $color);
    }

    public function setScoreboard(?Scoreboard $scoreboard): void {
        $this->scoreboard = $scoreboard;
        $scoreboard?->show();
    }

    public function updateScoreboard(): void {
        $this->setScoreboard($this->scoreboard);
    }

    public function addCooldown(Cooldown $cooldown): void {
        $this->cooldowns[$cooldown->getId()]  = $cooldown;
        $cooldown->setSession($this);
        $this->message("§8» §c" . $cooldown->getId() . " is now on cooldown.");
    }

    public function removeCooldown(Cooldown $cooldown): void {
        $id = $cooldown->getId();
        if($this->hasCooldown($id)) {
            unset($this->cooldowns[$id]);
            $this->message("§8» §a" . $cooldown->getId() . " is out of cooldown");
        }
    }

    public function updateNameTag(): void {
        $username = $this->getUsername();
        if($this->hasMatch() and $this->hasTeam()) {
            $this->player->setNameTag(ColorUtils::translate(
                $this->team->getColor() . $username . "\n" .
                "{WHITE}{BOLD}" . (int) $this->player->getHealth() . " {RED}❤"
            ));
        } else {
            $this->player->setNameTag(ColorUtils::translate("{GRAY}$username"));
        }
    }

    public function teleportToTeamSpawnPoint(bool $give_kit = true): void {
        $this->player->teleport($this->team->getWaitingPoint()); // TODO: Change the position to the spawnpoint
        $this->player->setHealth($this->player->getMaxHealth()); // TODO: Make a function for this?
        $this->player->getEffects()->clear();
        $this->updateNameTag();
        if($give_kit) {
            $this->setTheBridgeKit(ColorUtils::colorToDyeColor($this->getTeam()->getColor()));
        }
    }

    public function teleportToLobby(): void {
        $hunger_manager = $this->player->getHungerManager();
        $hunger_manager->setFood($hunger_manager->getMaxFood());
        $this->player->setHealth($this->player->getMaxHealth());
        $this->player->getEffects()->clear(); // TODO: Make a function for this?
        $this->player->setGamemode(GameMode::ADVENTURE());
        $this->setLobbyItems();
        $this->updateNameTag();
        $this->setScoreboard(new LobbyScoreboard($this));

        $this->player->teleport(Server::getInstance()->getWorldManager()->getWorldByName(ConfigGetter::getLobbyWorldName())->getSafeSpawn());
    }

    public function addLeaveMatchItem(): void {
        $this->clearInventory();
        $this->player->getInventory()->setItem(7, new LeaveMatchItem());
    }

    private function setLobbyItems(): void {
        $this->clearInventory();

        $inventory = $this->player->getInventory();
        $inventory->setItem(0, new ShopItem());
		$inventory->setItem(1, new UnlockedItem());
		$inventory->setItem(2, new SpectateItem());
		$inventory->setItem(4, new GameSelectorItem());
		$inventory->setItem(6, new PartyItem());
		$inventory->setItem(7, new ProfileItem());
		$inventory->setItem(8, new SettingsItem());

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
        return $this->player->getNetworkSession()->getPing() ?? 0;
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
