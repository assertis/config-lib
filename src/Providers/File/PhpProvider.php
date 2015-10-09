<?php

namespace Assertis\Configuration\Providers\File;

/**
 * @package Assertis\Configuration\Providers\File
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
class PhpProvider extends AbstractFileProvider
{
    const FILE_EXTENSION = 'json';

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        parent::__construct($path, self::FILE_EXTENSION);
    }


    protected function parse($file)
    {
        return include $file;
    }

}