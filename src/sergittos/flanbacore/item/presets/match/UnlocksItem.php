<?php

namespace sergittos\flanbacore\item\presets\match;

use sergittos\flanbacore\item\FlanbaItem;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class UnlocksItem extends FlanbaItem {
    
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
        $player->sendMessage("Coming soon...");
        return ItemUseResult::SUCCESS();
    }
}
