<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\utils\cooldown;


use sergittos\flanbacore\session\Session;

abstract class Cooldown {

    public const BOW = "Bow";
    public const GAPPLE = "Gapple";

    private string $id;
    private int $duration;

    protected int $time;
    protected Session $session;

    public function __construct(string $id, int $duration) {
        $this->id = $id;
        $this->duration = $duration;
        $this->time = $duration;
    }

    public function getId(): string {
        return $this->id;
    }

    public function getDuration(): int {
        return $this->duration;
    }

    public function setSession(Session $session): void {
        $this->session = $session;
    }

    public function onRun(): bool {
        $this->time--;
        if($this->time <= 0) {
            $this->session->removeCooldown($this);
            return true;
        }
        return false;
    }

}