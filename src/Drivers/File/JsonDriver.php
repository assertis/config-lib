<?php

namespace Assertis\Configuration\Drivers\File;

use Assertis\Configuration\ConfigurationJsonException;

/**
 * @package Assertis\Configuration\Drivers\File
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
class JsonDriver extends AbstractFileDriver
{
    const FILE_EXTENSION = 'json';

    /**
     * @param string $path
     */
    public function __construct(string $path)
    {
        parent::__construct($path, self::FILE_EXTENSION);
    }

    /**
     * @inheritdoc
     */
    protected function parse($file)
    {
        return json_decode(file_get_contents($file), true);
    }

    /**
     * @param $file
     * @throws ConfigurationJsonException
     */
    public function validate($file)
    {
        $path = $this->getFilePath($file);
        $json = file_get_contents($path);
        json_decode($json);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logError(sprintf("%s reading file %s from path %s: %s", json_last_error(), $file, $path, $json));
            throw new ConfigurationJsonException(json_last_error());
        }
    }
}
