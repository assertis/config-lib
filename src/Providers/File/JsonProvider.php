<?php

namespace Assertis\Configuration\Providers\File;

/**
 * @package Assertis\Configuration\Providers\File
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
class JsonProvider extends AbstractFileProvider
{
    const FILE_EXTENSION = 'json';

    /**
     * @var bool
     */
    private $assoc;

    /**
     * @param string $path
     * @param bool|true $assoc
     */
    public function __construct($path, $assoc = true)
    {
        $this->assoc = $assoc;
        parent::__construct($path, self::FILE_EXTENSION);
    }

    /**
     * @inheritdoc
     */
    protected function parse($file)
    {
        return json_decode(file_get_contents($file), $this->assoc);
    }

}