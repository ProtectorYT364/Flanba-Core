<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\party;


use pocketmine\player\Player;

class Party {

	private Player $owner;

	public function __construct(Player $owner){
		$this->owner = $owner;
	}

	public function getOwner() : Player{
		return $this->owner;
	}

	public function getPartyFactory() : PartyFactory{
		return new PartyFactory();
	}
}