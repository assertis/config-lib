<?php

namespace Assertis\Configuration\Drivers\File;

use Exception;

/**
 * @package Assertis\Configuration\Drivers\File
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
class XmlDriver extends AbstractFileDriver
{
    const FILE_EXTENSION = 'xml';

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        parent::__construct($path, self::FILE_EXTENSION);
    }

    /**
     * Parse file
     *
     * @param $file
     * @return array
     * @throws Exception
     */
    protected function parse($file)
    {
        return json_decode(json_encode((array)simplexml_load_string(file_get_contents($file))), true);
    }
}