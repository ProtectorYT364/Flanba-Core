<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\provider\presets;


use pocketmine\utils\Config;
use sergittos\flanbacore\FlanbaCore;
use sergittos\flanbacore\kit\Layout;
use sergittos\flanbacore\kit\TheBridgeKit;
use sergittos\flanbacore\provider\Provider;
use sergittos\flanbacore\session\Session;
use JsonException;

class JsonProvider extends Provider {

    public function loadSession(Session $session): void {
        $session->setKit(new TheBridgeKit(Layout::fromData($this->getSessionConfig($session)->get("layout"))));
    }

    /**
     * @throws JsonException
     */
    public function saveSession(Session $session): void {
        $config = $this->getSessionConfig($session);
        $layout = $session->getKit()->getLayout();
        $config->set("layout", [
            "sword_slot" => $layout->getSwordSlot(),
            "bow_slot" => $layout->getBowSlot(),
            "pickaxe_slot" => $layout->getPickaxeSlot(),
            "arrow_slot" => $layout->getArrowSlot(),

            "blocks_slots" => $layout->getBlocksSlots(),
            "gapples_slots" => $layout->getGapplesSlots()
        ]);
        $config->save();
    }

    /**
     * @throws JsonException
     */
    private function getSessionConfig(Session $session): Config {
        $config = new Config(FlanbaCore::getInstance()->getDataFolder() . "players/" . strtolower($session->getUsername()) . ".json", Config::JSON);;
        if(!$config->exists("layout")) {
            $config->set("layout", [
                "sword_slot" => 0,
                "bow_slot" => 1,
                "pickaxe_slot" => 2,
                "arrow_slot" => 8,

                "blocks_slots" => [
                    3 => 64,
                    4 => 64
                ],
                "gapples_slots" => [
                    5 => 8
                ]
            ]);
            $config->save();
        }
        return $config;
    }

}