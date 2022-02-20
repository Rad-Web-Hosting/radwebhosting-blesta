<?php

namespace ModulesGarden\DomainsReseller\Registrar\radwebhosting\core;

/**
 * Description of Loader
 *
 * @author Paweł Złamaniec <pawel.zl@modulesgarden.com>
 */
class Loader
{
    private $rootdir;

    public function __construct($dir)
    {
        $this->rootdir = $dir;
        $this->register();

        require_once $dir.DIRECTORY_SEPARATOR."vendor".DIRECTORY_SEPARATOR."autoload.php";
    }

    protected function register()
    {
        spl_autoload_register(function($className)
        {
            $namespace = str_replace("\\core","", __NAMESPACE__);
            if (strpos($className, $namespace) === 0)
            {
                $className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
                $path      = str_replace("ModulesGarden".DIRECTORY_SEPARATOR."DomainsReseller".DIRECTORY_SEPARATOR."Registrar".DIRECTORY_SEPARATOR."radwebhosting", $this->rootdir, $className) . '.php';

                $filename = basename($path);
                $file = str_replace(basename(strtolower($path)), $filename, strtolower($path));

                if (file_exists($file))
                {
                    require_once $file;
                }
            }
        });
    }
}
