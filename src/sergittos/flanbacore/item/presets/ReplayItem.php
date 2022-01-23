<?php

namespace sergittos\flanbacore\item\presets;

use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class ReplayItem extends \sergittos\flanbacore\item\FlanbaItem{

	public function __construct(){
		parent::__construct("{GOLD}Replay", ItemIds::TOTEM);
	}

	public function onClickAir(Player $player, Vector3 $directionVector) : ItemUseResult{
		$player->sendMessage(TextFormat::RED . "Coming soon...");
		return ItemUseResult::SUCCESS();
	}

}