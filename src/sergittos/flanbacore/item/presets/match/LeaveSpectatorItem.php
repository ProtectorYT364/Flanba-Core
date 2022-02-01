<?php


/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\item\presets\match;


use pocketmine\block\Block;
use pocketmine\block\utils\DyeColor;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use sergittos\flanbacore\item\FlanbaItem;
use sergittos\flanbacore\match\FlanbaMatch;
use sergittos\flanbacore\session\SessionFactory;
use sergittos\flanbacore\utils\scoreboard\presets\match\WaitingPlayersScoreboard;

class LeaveSpectatorItem extends FlanbaItem
{

    public function __construct()
    {
        parent::__construct("{RED}Leave match", ItemIds::BED, DyeColor::RED()->id());
    }

    public function onClickAir(Player $player, Vector3 $directionVector): ItemUseResult
    {
        $session = SessionFactory::getSession($player);
        $session->setMatch(null);
        $session->teleportToLobby();
        return ItemUseResult::SUCCESS();
    }

}