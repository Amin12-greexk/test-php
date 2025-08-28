<?php

namespace App\Helpers;

class NumberHelper
{
    public static function abbreviate($number)
    {
        if ($number < 1000000) {
            return round($number / 1000, 2) . 'K';
        } elseif ($number < 1000000000) {
            return round($number / 1000000, 2) . 'M';
        } else {
            return round($number / 1000000000, 2) . 'B';
        }
    }
}