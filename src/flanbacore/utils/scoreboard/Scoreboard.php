<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace flanbacore\utils\scoreboard;


use flanbacore\session\Session;
use flanbacore\utils\ConfigGetter;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;

class Scoreboard {

    protected string $id;
    protected Session|null $session = null;

    /** @var Line[] */
    private array $lines = [];

    public function __construct(string $id, ?Session $session) {
        $this->id = $id;
        $this->session = $session;
    }

    public function getId(): string {
        return $this->id;
    }

    public function getSession(): Session {
        return $this->session;
    }

    /**
     * @return Line[]
     */
    public function getLines(): array {
        return $this->lines;
    }

    public function addLine(Line $line): void {
        $score = $line->getScore();
        if(!($score > 15 or $score < 1)) {
            $entry = new ScorePacketEntry();
            $entry->objectiveName = $this->session->getUsername();
            $entry->type = $entry::TYPE_FAKE_PLAYER;
            $entry->customName = $line->getText();
            $entry->score = $score;
            $entry->scoreboardId = $score;
            $packet = new SetScorePacket();
            $packet->type = $packet::TYPE_CHANGE;
            $packet->entries[] = $entry;
            $this->session->sendDataPacket($packet);
        }
    }

    public function show(): void {
        if(!$this->session->getPlayer()->isOnline()) {
            return;
        }
        $this->hide();

        $packet = new SetDisplayObjectivePacket();
        $packet->displaySlot = "sidebar";
        $packet->objectiveName = $this->session->getUsername();
        $packet->displayName = ConfigGetter::getScoreboardTitle();
        $packet->criteriaName = "dummy";
        $packet->sortOrder = 0;
        $this->session->sendDataPacket($packet);
    }

    public function hide(): void {
        $packet = new RemoveObjectivePacket();
        $packet->objectiveName = $this->session->getUsername();
        $this->session->sendDataPacket($packet);
    }

}