<?php
namespace Assertis\Configuration\Scripts;

/**
 * This scripts allow you to add new base tenant configuration
 * to all environments
 *
 * @package Assertis\Configuration\Scripts
 * @author Łukasz Nowak <lukasz.nowak@assertis.co.uk>
 */
class AddTenant
{
    const DEFAULT_TENANT_MAP = 'config/schema/tenant.json';
    const CONFIG_DIR = 'config/';
    const IGNORE_CONFIGS = ['common.json'];
    /**
     * @var string
     */
    private $tenant;

    /**
     * @var string
     */
    private $tenantMapFile;
    /**
     * @var array
     */
    private $configFiles;

    public function __construct($argv = [])
    {
        $this->validateInput($argv);
        $this->run();
    }

    private function validateInput($argv = [])
    {
        if(empty($argv[1]) || empty($argv[2])) {
            echo 'To add tenant pass new tenant name and tenant map filename if it`s not default'.PHP_EOL;
            echo 'Example: php script/AddTenant.php newTenantName configFiles [schemaFile]'.PHP_EOL;
            die;
        }
        $this->tenant = $argv[1];
        $this->configFiles = explode(',', $argv[2]);


        $this->tenantMapFile = !empty($argv[3]) ? $argv[3] : self::DEFAULT_TENANT_MAP;
        if(!file_exists($this->tenantMapFile)) {
            echo 'File with tenant map doesn`t exist'.PHP_EOL;
            die;
        }

    }

    private function run()
    {
        foreach ($this->configFiles as $configFile) {
            $filePath = self::CONFIG_DIR.$configFile;
            $json = json_decode(file_get_contents($filePath), true);

            $tenantJsonBody = json_decode(file_get_contents($this->tenantMapFile), true);
            $json[$this->tenant] = $tenantJsonBody;
            file_put_contents($filePath, json_encode($json, JSON_PRETTY_PRINT));

            echo 'Tenant '.$this->tenant.' added to file '.$configFile.PHP_EOL;
        }
    }
}

new AddTenant($argv);