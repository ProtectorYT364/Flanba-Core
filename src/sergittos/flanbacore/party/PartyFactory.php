<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\party;


use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use sergittos\flanbacore\FlanbaCore;

class PartyFactory {

    public array $parties = [];

    public array $invites = [];

    public function createParty(Player $owner)
    {
        $members = "";
        if (!array_key_exists($owner->getName(), $this->parties)) {
            $this->parties[$owner->getName()] = [$owner->getName(), $members];
            $owner->sendMessage(TextFormat::GREEN . "Successfully created a party!");
        } else {
            $owner->sendMessage(TextFormat::RED . "You already have a party!");
        }
    }

    public function InvitePlayer(Player $owner, $iplayer){
        $player = FlanbaCore::getInstance()->getServer()->getPlayerExact($iplayer);
        if($player == null){
            $owner->sendMessage(TextFormat::RED . "Unknown player, maybe you misspelled his name?");
        } else {
            if(!$this->hasParty($owner)){
                $owner->sendMessage(TextFormat::RED . "You don't have a party!, do \"/party create\" to create one!");
            } else {
                if($this->hasParty($player)){
                    $owner->sendMessage(TextFormat::RED . $player->getName() . " already has a party!");
                } else {
                    new PartyInvitation($owner, $player);
                }
            }
        }
    }

    public function JoinParty(Player $owner, Player $player){
        $this->parties[$owner->getName()] = $player;
        $player->sendMessage(TextFormat::GREEN . "You have successfully joined " . TextFormat::AQUA . "{$owner->getName()}'s" . TextFormat::GREEN . " party!");
    }

    public function getAllParties(){
        $parties = [];
        foreach($this->parties as $list){
            $parties = $list;
        }
        return $parties;
    }

    public function getParty(Player $player) : Party {
        return new Party($player);
    }

    public function hasParty(Player $player) : bool{
        if(array_key_exists($player->getName(), $this->parties)){
            $idk = true;
        } else {
            $idk = false;
        }
        return $idk;
    }

    public function addInvite(Player $player, Player $iplayer){
        $this->invites[$iplayer->getName()] = $player->getName();
    }
}