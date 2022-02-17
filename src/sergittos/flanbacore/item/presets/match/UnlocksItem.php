<?php

namespace DidntPot\items\types;

use DidntPot\items\LobbyItem;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class SettingsItem extends LobbyItem
{
    public function __construct()
    {
        parent::__construct("§6Unlocks §8[Use]", 450);
    }

    /**
     * @param Player $player
     * @param Vector3 $directionVector
     * @return ItemUseResult
     */
    public function onClickAir(Player $player, Vector3 $directionVector): ItemUseResult
    {
        $player->sendMessage(TextFormat::RED . "Coming soon...");
        return ItemUseResult::SUCCESS();
    }
}
