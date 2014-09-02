<?php

namespace ecoreng\SlimBase;

class Manager
{

    protected static $theme = 'SlimBase\\Theme';

    public static function setTheme($theme)
    {
        self::$theme = $theme;
    }

    public static function getTheme()
    {
        return self::$theme;
    }

    
}
