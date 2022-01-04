<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\kit;


use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\VanillaItems;

class TheBridgeKit extends Kit {

    public function getId(): int {
        return self::THE_BRIDGE;
    }

    public function getArmorContents(): array {
        $unbreaking = new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 10);
        return [
            VanillaItems::LEATHER_CAP()->addEnchantment($unbreaking),
            VanillaItems::LEATHER_TUNIC()->addEnchantment($unbreaking),
            VanillaItems::LEATHER_PANTS()->addEnchantment($unbreaking),
            VanillaItems::LEATHER_BOOTS()->addEnchantment($unbreaking)
        ];
    }

    public function getItems(): array {
        $terracotta = BlockFactory::getInstance()->get(BlockLegacyIds::TERRACOTTA, 0)->asItem();
        $blocks = $terracotta->setCount($terracotta->getMaxStackSize());
        $air = VanillaBlocks::AIR()->asItem();
        return [
            VanillaItems::IRON_SWORD(),
            VanillaItems::BOW(),
            VanillaItems::DIAMOND_PICKAXE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 2)),
            $blocks,
            $blocks,
            VanillaItems::GOLDEN_APPLE()->setCount(8),
            $air,
            $air,
            VanillaItems::ARROW()
        ];
    }

}