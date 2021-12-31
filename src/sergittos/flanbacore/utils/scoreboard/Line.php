<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\utils\scoreboard;


use sergittos\flanbacore\utils\ColorUtils;

class Line {

    private int $score;
    private string $text;

    public function __construct(int $score, string $text) {
        $this->score = $score;
        $this->text = ColorUtils::translate($text);
    }

    public function getScore(): int {
        return $this->score;
    }

    public function getText(): string {
        return $this->text;
    }

}