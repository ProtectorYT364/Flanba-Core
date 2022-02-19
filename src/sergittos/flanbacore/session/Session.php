<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\session;


use alemiz\sga\StarGateAtlantis;
use muqsit\invmenu\InvMenu;
use pocketmine\scheduler\ClosureTask;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;
use paroxity\portal\Portal;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\DyeColorIdMap;
use pocketmine\inventory\PlayerInventory;
use pocketmine\item\Arrow;
use pocketmine\item\Bow;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\GoldenApple;
use pocketmine\item\ItemIds;
use pocketmine\item\Pickaxe;
use pocketmine\item\Sword;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\ClientboundPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\Server;
use sergittos\flanbacore\FlanbaCore;
use sergittos\flanbacore\item\presets\GameSelectorItem;
use sergittos\flanbacore\item\presets\match\EditKitItem;
use sergittos\flanbacore\item\presets\match\VoteMapItem;
use sergittos\flanbacore\item\presets\match\layout\HotbarItem;
use sergittos\flanbacore\item\presets\match\LeaveMatchItem;
use sergittos\flanbacore\item\presets\match\LeaveSpectatorItem;
//use sergittos\flanbacore\item\presets\SpectateItem;
use sergittos\flanbacore\kit\Kit;
use sergittos\flanbacore\kit\Layout;
use sergittos\flanbacore\match\FlanbaMatch;
use sergittos\flanbacore\match\team\Team;
use sergittos\flanbacore\utils\ColorUtils;
use sergittos\flanbacore\utils\ConfigGetter;
use sergittos\flanbacore\utils\cooldown\Cooldown;
use sergittos\flanbacore\utils\scoreboard\presets\LobbyScoreboard;
use sergittos\flanbacore\utils\scoreboard\presets\match\WaitingPlayersScoreboard;
use sergittos\flanbacore\utils\scoreboard\Scoreboard;

class Session {

    private Player $player;

    private FlanbaMatch|null $match = null;
    private Team|null $team = null;
    private Scoreboard|null $scoreboard = null;
    private Kit $kit;

	private int $current_ping;


	/** @var Cooldown[] */
    private array $cooldowns = [];

    public function __construct(Player $player) {
        $this->player = $player;
		$this->current_ping = $this->getPing();
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

    public function getKit(): Kit {
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

    public function hasCooldown(string $id): bool {
        return array_key_exists($id, $this->cooldowns);
    }


    public function setMatch(?FlanbaMatch $match): void {
        $finish = true;
        if($this->hasMatch()) {
            $stage = $this->match->getStage();
            if($stage === FlanbaMatch::COUNTDOWN_STAGE) {
                $this->match->setStage(FlanbaMatch::WAITING_STAGE);
                $this->match->setCountdown(ConfigGetter::getCountdownSeconds());
                foreach($this->match->getPlayers() as $player) {
                    $player->setScoreboard(new WaitingPlayersScoreboard($player, $this->match));
                }
                $finish = false;
            } elseif($stage === FlanbaMatch::WAITING_STAGE or $stage === FlanbaMatch::ENDING_STAGE) {
                $finish = false;
            }
            $this->match->removeSession($this, $finish);
        }
        $this->match = $match;
    }

    public function setTeam(?Team $team): void {
        $this->team?->removeMember($this);
        $this->team = $team;
    }

    public function setKit(Kit $kit): void {
        $this->kit = $kit;
    }

    public function giveTheBridgeKit(): void {
        $this->clearInventory();
        $color = ColorUtils::colorToDyeColor($this->team->getColor());
        $this->player->getInventory()->setContents($this->kit->getItems($color));
        $this->player->getArmorInventory()->setContents($this->kit->getArmorContents($color));
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
		if($this->getPlayer()->isOnline()){
			$this->message("§8» §c" . $cooldown->getId() . " is now on cooldown.");
		}
    }

    public function removeCooldown(Cooldown $cooldown): void {
        $id = $cooldown->getId();
        if($this->hasCooldown($id)) {
            unset($this->cooldowns[$id]);
			if($this->getPlayer()->isOnline()){
				$this->message("§8» §a" . $cooldown->getId() . " is out of cooldown");
			}
        }
    }

    public function updateNameTag(): void {
        $username = $this->getUsername();
        if($this->hasMatch() and $this->hasTeam()) {
            $this->player->setNameTag(ColorUtils::translate(
                $this->team->getColor() . $username . "\n" .
                "{WHITE}" . (int) $this->player->getHealth() . ""
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
            $this->giveTheBridgeKit();
        }
    }

    public function teleportToLobby(): void {
       StarGateAtlantis::getInstance()->transferPlayer($this->getPlayer(), 'Hub1');
    }

    public function setMatchItems(): void {
        $this->clearInventory();

        $inventory = $this->player->getInventory();
        $inventory->setItem(0, new EditKitItem());
        $inventory->setItem(8, new LeaveMatchItem());
	    $inventory->setItem(7, new VoteMapItem());
    }

    public function setSpectatorItems(): void {
        $this->clearInventory();

        $inventory = $this->player->getInventory();
        $inventory->setItem(7, new LeaveSpectatorItem());
    }
    
    public function sendEditKitMenu(): void {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->setName("Edit kit");

        $contents = [];
        for($index = 27; $index <= 35; $index++) {
            $contents[$index] = new HotbarItem();
        }
        $color = DyeColor::GREEN();
        foreach($this->kit->getItems($color) as $index => $item) {
            $contents[Layout::switchPlayerInventoryIndexToUIIndex($index)] = $item;
        }
        $contents[48] = VanillaBlocks::CHEST()->asItem()->setCustomName(ColorUtils::translate("{GREEN}Save layout"));
        $contents[50] = VanillaBlocks::BARRIER()->asItem()->setCustomName(ColorUtils::translate("{RED}Reset layout"));
        $inventory = $menu->getInventory();
        $inventory->setContents($contents);

        $menu->setListener(function(InvMenuTransaction $transaction) use ($inventory, $contents, $color): InvMenuTransactionResult {
            $discard = $transaction->discard();
            $id = $transaction->getItemClicked()->getId();
            if($id === ItemIds::CHEST) {
                $layout = $this->kit->getLayout();

                $gapples = [];
                $blocks = [];
                foreach($inventory->getContents() as $index => $item) {
                    $count = $item->getCount();
                    $index = Layout::switchUIIndexToPlayerInventoryIndex($index);
                    if($item instanceof Sword) {
                        $layout->setSwordSlot($index);
                    } elseif($item instanceof Bow) {
                        $layout->setBowSlot($index);
                    } elseif($item instanceof Pickaxe) {
                        $layout->setPickaxeSlot($index);
                    } elseif($item instanceof Arrow) {
                        $layout->setArrowSlot($index);
                    } elseif($item instanceof GoldenApple) {
                        $gapples[$index] = $count;
                    } elseif($item->getId() === ItemIds::TERRACOTTA) {
                        $blocks[$index] = $count;
                    }
                }
                if(!empty($gapples)) {
                    $layout->setGapplesSlots($gapples);
                }
                if(!empty($blocks)) {
                    $layout->setBlocksSlots($blocks);
                }
                $this->sendOrbSound();
                return $discard;
            } elseif($id === ItemIds::BARRIER) {
                $air = VanillaBlocks::AIR()->asItem();
                for($index = 0; $index <= 26; $index++) {
                    $contents[$index] = $air;
                }
                for($index = 36; $index <= 44; $index++) {
                    $contents[$index] = $air;
                }
                $terracotta = BlockFactory::getInstance()->get(BlockLegacyIds::TERRACOTTA, DyeColorIdMap::getInstance()->toId($color))->asItem();
                $blocks = $terracotta->setCount($terracotta->getMaxStackSize());
                $contents[36] = VanillaItems::IRON_SWORD();
                $contents[37] = VanillaItems::BOW();
                $contents[38] = VanillaItems::DIAMOND_PICKAXE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 2));
                $contents[39] = $blocks;
                $contents[40] = $blocks;
                $contents[41] = VanillaItems::GOLDEN_APPLE()->setCount(8);
                $contents[44] = VanillaItems::ARROW();
                $inventory->setContents($contents);
                return $discard;
            }
            foreach($transaction->getTransaction()->getInventories() as $inventory) {
                if($inventory instanceof PlayerInventory) {
                    return $discard;
                }
            }
            return $transaction->continue();
        });
        $menu->send($this->player);
    }

    public function setLobbyItems(): void {
        $this->clearInventory();

        $inventory = $this->player->getInventory();
        $inventory->setItem(2, new GameSelectorItem());
        $inventory->setItem(7, new LeaveMatchItem());
    }

    private function clearInventory(): void {
        $this->player->getInventory()->clearAll();
        $this->player->getArmorInventory()->clearAll();
    }

    public function setImmobile(bool $immobile = true): void {
        $this->player->setImmobile($immobile);
    }

    public function sendOrbSound(): void {
        $position = $this->player->getPosition();
        $packet = new PlaySoundPacket();
        $packet->soundName = "random.orb";
        $packet->x = $position->getX();
        $packet->y = $position->getY();
        $packet->z = $position->getZ();
        $packet->volume = 1;
        $packet->pitch = 1;
        $this->sendDataPacket($packet);
    }

    public function sendDataPacket(ClientboundPacket $packet): void {
        $this->player->getNetworkSession()->sendDataPacket($packet);
    }

    public function getPing(): int {
		if(!$this->player->isOnline()) {
			return 0;
		}
        return $this->player->getNetworkSession()->getPing() ?? 0;
    }

	public function checkPing(): bool {
		$ping = $this->getPing();
		if($this->current_ping !== $ping) {
			$this->current_ping = $ping;
			return true;
		}
		return false;
	}

    public function getUsername(): string {
        return $this->player->getName();
    }

    public function popup(string $popup): void {
        if($this->player->isConnected()) $this->player->sendPopup(ColorUtils::translate($popup));
    }

    public function title(string $title, string $subtitle = ""): void {
	if($this->player->isConnected()) $this->player->sendTitle(ColorUtils::translate($title), ColorUtils::translate($subtitle));
    }

    public function message(string $message): void {
        if($this->player->isConnected()) $this->player->sendMessage(ColorUtils::translate($message));
    }

    public function save(): void {
        FlanbaCore::getInstance()->getProvider()->saveSession($this);
    }

}
