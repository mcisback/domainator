<?php

namespace App\LpMachine\Helpers;

class TranslateHelper
{
    public function __construct($translations) {
        $this->translations = $translations;
    }

    public function translate($string) {
        return $this->translations[$string] ?? $string;
    }

    public function __invoke($string)
    {
        return $this->translate($string);
    }
}
