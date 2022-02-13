<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\item\presets;


use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use sergittos\flanbacore\FlanbaCore;
use sergittos\flanbacore\form\queue\GameSelectorForm;
use sergittos\flanbacore\form\queue\PlayForm;
use sergittos\flanbacore\item\FlanbaItem;
use sergittos\flanbacore\session\SessionFactory;
use sergittos\flanbacore\utils\ConfigGetter;

class GameSelectorItem extends FlanbaItem {

    public function __construct() {
        parent::__construct("{RED}Play Again", ItemIds::COMPASS);
    }

    public function onClickAir(Player $player, Vector3 $directionVector): ItemUseResult {
        FlanbaCore::getInstance()->getQueueManager()->getQueueByCapacity(ConfigGetter::getGamemodeMax())->addSession(
            SessionFactory::getSession($player)
        );
        return ItemUseResult::SUCCESS();
    }

}