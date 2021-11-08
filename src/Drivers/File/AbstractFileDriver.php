<?php

namespace Assertis\Configuration\Drivers\File;

use Assertis\Configuration\Drivers\DriverInterface;
use Exception;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * @package Assertis\Configuration\Drivers
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
abstract class AbstractFileDriver implements DriverInterface, LoggerAwareInterface
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $fileExtension;

    /**
     * @var array[]
     */
    private $cache = [];

    /**
     * @var LoggerInterface | null
     */
    private $logger;

    /**
     * @param string $path
     * @param string $fileExtension
     */
    public function __construct($path, $fileExtension)
    {
        $this->path = $path;
        $this->fileExtension = $fileExtension;
    }

    /**
     * @inheritdoc
     */
    public function getSettings($key)
    {
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        $config = $this->parse($this->getFilePath($key));
        $this->cache[$key] = $config;

        return $config;
    }

    /**
     * @inheritdoc
     */
    public function keyExists($key)
    {
        return is_readable($this->getFilePath($key));
    }

    /**
     * Return file path
     *
     * @param string $name name of file
     * @return string
     */
    protected function getFilePath($name)
    {
        //Add directory separator if not exists in path
        if (substr($this->path, -1) !== DIRECTORY_SEPARATOR && substr($name, 1) !== DIRECTORY_SEPARATOR) {
            $this->path = $this->path . DIRECTORY_SEPARATOR;
        }

        return $this->path . $name . '.' . $this->fileExtension;
    }

    /**
     * @return string
     */
    public function getFileExtension()
    {
        return $this->fileExtension;
    }

    /**
     * Parse file
     *
     * @param $file
     * @return array
     * @throws Exception
     */
    abstract protected function parse($file);

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    protected function logError(string $message): void {
        if (!$this->logger) {
            return;
        }

        $this->logger->error($message);
    }
}
