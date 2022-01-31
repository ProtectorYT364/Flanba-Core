<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\utils\scoreboard;


use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use sergittos\flanbacore\session\Session;
use sergittos\flanbacore\utils\ColorUtils;
use sergittos\flanbacore\utils\ConfigGetter;

abstract class Scoreboard {

    protected Session|null $session = null;

    public function __construct(?Session $session) {
        $this->session = $session;
    }

    public function getSession(): Session {
        return $this->session;
    }

    /**
     * @return string[]
     */
    abstract public function getLines(): array;

    private function addLine(Line $line): void {
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
        $packet->displayName = "flanba.sb.logo";
        $packet->criteriaName = "dummy";
        $packet->sortOrder = 0;
        $this->session->sendDataPacket($packet);

        $current_number = 0;
        foreach($this->getLines() as $line) {
            $current_number++;
            $this->addLine(new Line($current_number, $line));
        }
    }

    private function hide(): void {
        $packet = new RemoveObjectivePacket();
        $packet->objectiveName = $this->session->getUsername();
        $this->session->sendDataPacket($packet);
    }

}