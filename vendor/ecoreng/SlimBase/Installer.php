<?php

namespace ecoreng\SlimBase;

class Installer
{

    /**
     * InstallCacheFile path
     * 
     * @var string 
     */
    protected $installCacheFile = '';

    /**
     * Inits the Manager Class
     * 
     * @param string $installCacheFile
     */
    public function __construct($installCacheFile)
    {
        $this->installCacheFile = $installCacheFile;
    }

    /**
     * Checks if there are plugins that need installation, calls the installer
     * routine if a plugin is new, or the uninstaller routine if the plugin was 
     * removed
     * 
     * @param array $pluginsToLoad
     * @throws \RuntimeException
     */
    public function checkInstall($pluginsToLoad)
    {
        $pluginsChecksum = self::generateChecksum($pluginsToLoad);
        if (!self::arePluginsInstalled($this->installCacheFile, $pluginsChecksum)) {

            self::installerAction(self::getPluginsToInstall($pluginsToLoad), 'install');
            self::installerAction(self::getPluginsToUninstall($pluginsToLoad), 'uninstall');

            self::updateSymlinks($pluginsToLoad);

            if (!self::updateCache($this->installCacheFile, $pluginsChecksum, $pluginsToLoad)) {
                throw new \RuntimeException('InstallCacheFile update failed');
            }
        }
    }

    /**
     * Loads the Bootstraps of the active installed plugins
     * 
     * @param array $pluginsToLoad
     */
    public function loadBootstraps($pluginsToLoad)
    {
        $app = \Slim\Slim::getInstance();
        $pluginsFolder = $app->config('pluginsFolder');
        foreach ($pluginsToLoad as $plugin) {
            $plugin = str_replace('\\', DS, $plugin);
            require ('..' . DS . $pluginsFolder . DS . $plugin . DS . 'bootstrap.php');
        }
    }

    /**
     * Contains the previously installed plugins in an array
     * 
     * @var array 
     */
    protected static $previouslyInstalled = array();

    /**
     * Generates the checksum of the array given
     * 
     * @param array $pluginsToLoad
     * @return string
     */
    protected static function generateChecksum($plugins)
    {
        return md5(var_export($plugins, true));
    }

    /**
     * Returns true if all the plugins given are installed
     * 
     * @param string $installCacheFile
     * @param array $pluginsChecksum
     * @return boolean
     */
    protected static function arePluginsInstalled($installCacheFile, $pluginsChecksum)
    {
        require($installCacheFile);
        if ($pluginsChecksum !== $installChecksum) {
            self::$previouslyInstalled = $installedPlugins;
            return false;
        } else {
            return true;
        }
    }

    /**
     * Returns the previously installed plugins
     * 
     * @return array
     */
    protected static function getPreviouslyInstalledPlugins()
    {
        return self::$previouslyInstalled;
    }

    /**
     * Takes an array of plugins and compares it to the previously installed
     * plugins, and returns the plugins that werent installed before
     * 
     * @param array $pluginsToLoad
     * @return array
     */
    protected static function getPluginsToInstall($pluginsToLoad)
    {
        return array_diff($pluginsToLoad, self::getPreviouslyInstalledPlugins());
    }

    /**
     * Takes an array of plugins and compares it to the previously installed
     * plugins, and returns the plugins that are no longer needed
     * 
     * @param array $pluginsToLoad
     * @return array
     */
    protected static function getPluginsToUninstall($pluginsToLoad)
    {
        return array_diff(self::getPreviouslyInstalledPlugins(), $pluginsToLoad);
    }

    /**
     * Takes an array of plugins, loads its installer and executes the action
     * provided by $action
     * 
     * @param array $plugins
     * @param string $action
     */
    protected static function installerAction($plugins, $action)
    {
        foreach ($plugins as $plugin) {
            $plugin = $plugin . '\\Installer';
            if (class_exists($plugin)) {
                $installer = new $plugin;
                $installer->{$action}();
            }
        }
    }

    /**
     * Updates the InstallCacheFile with the new plugin list and the new
     * checksum
     * 
     * @param string $installCacheFile
     * @param string $pluginsChecksum
     * @param array $pluginsToLoad
     * @return boolean
     */
    protected static function updateCache($installCacheFile, $pluginsChecksum, $pluginsToLoad)
    {

        $pluginsLoaded = var_export($pluginsToLoad, true);
        $data = <<<EOT
<?php
\x24installedPlugins = $pluginsLoaded;
\x24installChecksum = '$pluginsChecksum';
EOT;
        return file_put_contents($installCacheFile, $data);
    }

    /**
     * Updates the symlinks to public files for each plugin so they are available
     * publicly inside the global (duh) public folder.
     * 
     * @param array $plugins
     */
    protected static function updateSymlinks($plugins)
    {
        $app = \Slim\Slim::getInstance();
        $dirExists = true;
        if (!is_dir('plugins')) {
            $dirExists = @mkdir('plugins');
        }
        $pluginsFolder = $app->config('pluginsFolder');
        foreach ($plugins as $plugin) {
            $plugin = str_replace('\\', DS, $plugin);
            if ($dirExists) {
                if (!@readlink('plugins' . DS . $plugin)) {
                    $location = '..' . DS . $pluginsFolder . DS . $plugin . DS . 'public' . DS;
                    $destination = 'plugins' . DS . $plugin;
                    if (file_exists($location)) {

                        $location = '..' . DS . '..' . DS . $location;
                        $destination = $destination;

                        if (!is_dir(dirname($destination))) {
                            $dirExists = @mkdir(dirname($destination));
                        }

                        $dirExists = symlink($location, $destination);
                        if (!$dirExists) {
                            if (function_exists('exec')) {
                                exec('ln -s ' . $location . ' ' . $destination);
                            }
                        }
                    }
                }
            }
        }
    }

}
