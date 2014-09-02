<?php

namespace ecoreng\SlimBase;

class PluginRouter
{

    protected static $pluginFolder;

    public static function init($pluginFolder)
    {
        self::$pluginFolder = $pliginFolder;
    }

    public static function pt($namespace, $module, $method = null, $type = 'controller')
    {
        return self::path($namespace, $module, $method, $type);
    }

    public static function view($namespace, $plugin, $module, $type = 'view')
    {
        return self::path($namespace, $plugin, $module, $type);
    }

    public static function path($namespace, $module, $method = null, $type)
    {
        switch ($type) {
            case "controller":
                if ($method !== null) {
                    return '\\' . $namespace . '\\' . ucfirst($type) . '\\' . $module . ':' . $method;
                } else {
                    return '\\' . $namespace . '\\' . ucfirst($type) . '\\' . $module;
                }
                break;
            case "view":
                    return '../plugins' . DS . $namespace . DS . $module . DS . ucfirst($type) . DS . $method . '.php';
                break;
        }
    }

}
