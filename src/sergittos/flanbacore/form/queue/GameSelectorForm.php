<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\flanbacore\form\queue;


use EasyUI\variant\SimpleForm;

class GameSelectorForm extends SimpleForm {

    public function __construct() {
        parent::__construct("Game selector");
    }

    protected function onCreation(): void {
        $this->addRedirectFormButton("Solo (1 vs 1)", new PlayForm(1));
        $this->addRedirectFormButton("Duo (2 vs 2)", new PlayForm(2));
        $this->addRedirectFormButton("Trios (3 vs 3)", new PlayForm(3));
        $this->addRedirectFormButton("Squad (4 vs 4)", new PlayForm(4));
    }

}