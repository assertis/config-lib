<?php
declare(strict_types=1);

namespace Assertis\Configuration;

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

    const URI_KEY = 'REQUEST_URI';
    private const PATH_KEY = 'DOCUMENT_URI';

    /**
     * @var array
     */
    private $serverVariables;
    /**
     * @var array
     */
    private $urlParams;
    /**
     * @var array
     */
    private $extra;

    /**
     * @param array $serverVariables
     * @param array $urlParams
     * @param array $extra
     */
    public function __construct(array $serverVariables, array $urlParams, array $extra = [])
    {
        $this->serverVariables = $serverVariables;
        $this->urlParams = $urlParams;
        $this->extra = array_combine(array_map('strtolower', array_keys($extra)), array_values($extra));
    }

    /**
     * @return string|null
     */
    public function getEnv(): ?string
    {
        return $this->getValue(self::ENV_KEY, self::ENV_DEFAULT);
    }

    /**
     * @return string|null
     */
    public function getTenant(): ?string
    {
        return $this->getValue(self::TENANT_KEY, self::TENANT_DEFAULT);
    }

    /**
     * @return bool
     */
    public function isDev(): bool
    {
        return in_array($this->getEnv(), [ConfigurationFactory::ENV_DEV, ConfigurationFactory::ENV_INTEGRATION], true);
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed|null
     */
    public function getValue(string $name, $default = null)
    {
        return $this->getExtraParam($name)
            ?? $this->getUrlParam($name)
            ?? $this->getHeader($name)
            ?? $this->getEnvironment($name)
            ?? $default;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    private function getExtraParam(string $name)
    {
        return $this->extra[strtolower($name)] ?? null;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    private function getUrlParam(string $name)
    {
        $key = strtolower($name);

        return array_key_exists($key, $this->urlParams) ? $this->urlParams[$key] : null;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    private function getEnvironment(string $name)
    {
        return array_key_exists($name, $this->serverVariables) ? $this->serverVariables[$name] : null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasHeader(string $name): bool
    {
        return array_key_exists($this->getHeaderKey($name), $this->serverVariables);
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function getHeader(string $name)
    {
        return $this->hasHeader($name) ?
            $this->serverVariables[$this->getHeaderKey($name)] :
            null;
    }

    /**
     * @param string $name
     * @return string
     */
    private function getHeaderKey(string $name): string
    {
        return 'HTTP_X_' . str_replace('-', '_', strtoupper($name));
    }

    /**
     * @return string|null
     */
    public function getRequestUri(): ?string
    {
        return array_key_exists(self::URI_KEY, $this->serverVariables) ?
            $this->serverVariables[self::URI_KEY] :
            null;
    }

    public function getPathInfo(): ?string
    {
        return array_key_exists(self::PATH_KEY, $this->serverVariables) ?
            $this->serverVariables[self::PATH_KEY] :
            null;
    }
}
