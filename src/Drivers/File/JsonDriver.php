<?php

namespace Assertis\Configuration\Drivers\File;

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
    public function __construct($path)
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

}