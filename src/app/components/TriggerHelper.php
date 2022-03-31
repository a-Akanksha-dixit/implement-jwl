<?php

namespace App\Components;

class TriggerHelper
{
    private static $flagvalue = false;


    public static function hasAlreadyfired()
    {
        return self::$flagvalue;
    }
    
    public static function setAlreadyfired()
    {
        self::$flagvalue = true;
    }
}
