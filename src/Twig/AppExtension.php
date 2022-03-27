<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('getDiff', [$this, 'difficultyToString']),
        ];
    }

    public function difficultyToString(int $index): string
    {
        $difficulty = [
            '-',
            '3a','3a+','3b','3b+','3c','3c+',
            '4a','4a+','4b','4b+','4c','4c+',
            '5a','5a+','5b','5b+','5c','5c+',
            '6a','6a+','6b','6b+','6c','6c+',
            '7a','7a+','7b','7b+','7c','7c+',
            '8a','8a+','8b','8b+','8c','8c+',
            '9a','9a+','9b','9b+','9c','9c+',
        ];

        if($index < 0 || $index > sizeof($difficulty)) {
            return '-';
        }

        return $difficulty[$index];
    }
}