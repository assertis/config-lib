<?php

namespace Assertis\Configuration;

use Silex\Application;

/**
 * Turns out most things are located in the $_SERVER variable.
 *  - HTTP headers are at 'HTTP_'.str_replace('-', '_', strtoupper($key))
 *  - Command line (`FOO=bar php test.php`) environment settings are as defined.
 *  - Apache SetEnv settings are as defined.
 * URL parameters are in the $_GET, as usual.
 *
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class RuntimeSettings
{
    const ENV_KEY = 'ENV';
    const ENV_DEFAULT = ConfigurationFactory::DEFAULT_KEY;

    const TENANT_KEY = 'TENANT';
    const TENANT_DEFAULT = null;

    /**
     * @var array
     */
    private $serverVariables;
    /**
     * @var array
     */
    private $urlParams;

    /**
     * @param array $serverVariables
     * @param array $urlParams
     */
    public function __construct(array $serverVariables, array $urlParams)
    {
        $this->serverVariables = $serverVariables;
        $this->urlParams = $urlParams;
    }

    /**
     * @return string
     */
    public function getEnv()
    {
        return self::getValue(self::ENV_KEY, self::ENV_DEFAULT);
    }

    /**
     * @return string
     */
    public function getTenant()
    {
        return self::getValue(self::TENANT_KEY, self::TENANT_DEFAULT);
    }

    /**
     * @return bool
     */
    public function isDev()
    {
        return in_array(self::getEnv(), [ConfigurationFactory::ENV_DEV, ConfigurationFactory::ENV_INTEGRATION]);
    }

    /**
     * @param string $name
     * @param string $default
     * @return string
     */
    public function getValue($name, $default = null)
    {
        $url = $this->getUrlParam($name);
        if (null !== $url) {
            return $url;
        }

        $header = $this->getHeader($name);
        if (null !== $header) {
            return $header;
        }

        $environment = $this->getEnvironment($name);
        if (null !== $environment) {
            return $environment;
        }

        return $default;
    }

    /**
     * @param string $name
     * @return string|null
     */
    private function getUrlParam($name)
    {
        $key = strtolower($name);

        return array_key_exists($key, $this->urlParams) ? $this->urlParams[$key] : null;
    }

    /**
     * @param string $name
     * @return string|null
     */
    private function getEnvironment($name)
    {
        return array_key_exists($name, $this->serverVariables) ? $this->serverVariables[$name] : null;
    }

    /**
     * @param string $name
     * @return string|null
     */
    private function getHeader($name)
    {
        $key = 'HTTP_X_' . str_replace('-', '_', strtoupper($name));

        return array_key_exists($key, $this->serverVariables) ? $this->serverVariables[$key] : null;
    }
}
