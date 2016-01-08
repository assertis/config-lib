<?php

namespace Assertis\Configuration\Drivers\File;

/**
 * @package Assertis\Configuration\Drivers\File
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
class PhpDriver extends AbstractFileDriver
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
     * @param string $file
     * @return array
     */
    protected function parse($file)
    {
        return include $file;
    }
}
