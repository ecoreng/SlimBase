<?php

//error_reporting(E_ALL);
//ini_set("display_errors", 1);

// Set default timezone
date_default_timezone_set("America/Los_Angeles");

// set DIRECTORY_SEPARATOR as DS
define('DS', DIRECTORY_SEPARATOR);

// PSR-0 autoloader in plugins folder
$pluginsFolder = 'plugins';
spl_autoload_register(function ($class) use ($pluginsFolder) {
    $file = $pluginsFolder . DS . preg_replace('#\\\|_(?!.+\\\)#', '/', $class) . '.php';
    if (stream_resolve_include_path($file))
        require $file;
});

// Eloquent instantiation
use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;
require_once('appDbConfig.php');
$capsule->addConnection($dbConfig, 'default');
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Config
$app->config(
        array(
            'debug' => true,
            'view' => new \ecoreng\SimpleLayout\View(),
            'defaultLayout' => array('SlimBase', 'Theme', 'default'),
            'installCacheFile' => '../tmp/cache/install/install.php',
            'publicFolder' => 'public',
            'pluginsFolder' => $pluginsFolder,
        )
);

// Plugins to load
$pluginsToLoad = array(
    'SlimBase\\Theme',
);

// Autoload plugin Installer from vendor folder
use \ecoreng\SlimBase\Installer;

// Install plugin bootstraps from the plugins listed in array if not already installed
$Plugins = new Installer($app->config('installCacheFile'));
$Plugins->checkInstall($pluginsToLoad);
$Plugins->loadBootstraps($pluginsToLoad);

// Set plugin location folder and theme
use \ecoreng\SlimBase\Manager as Mngr;
$app->hook('ecoreng.plugin.beforeAction', function ($args) use ($app) {
    $root = str_replace($_SERVER['DOCUMENT_ROOT'], '', getcwd());
    $root = $root != '' ? DS . $root : $root;
    $pluginsBaseUrl = $root . DS . $app->config('pluginsFolder') . DS;
    $app->view->appendData(
            array(
                'pluginBaseUrl' => $pluginsBaseUrl . $args['plugin'] . DS,
                'pluginsBaseUrl' => $root . DS . 'plugins' . DS
            )
    );
    $app->view->appendData(array('theme' => str_replace('\\', DS, Mngr::getTheme())));
});
