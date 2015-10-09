<?php

namespace Assertis\Configuration\Providers\File;

use Assertis\Configuration\Providers;

/**
 * @package Assertis\Configuration\Providers\File
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
class IniProvider extends AbstractFileProvider
{
    const FILE_EXTENSION = 'ini';

    /**
     * @var boolean
     */
    private $processSections;

    /**
     * @param string $path
     * @param bool|false $processSections
     */
    public function __construct($path, $processSections = false)
    {
        $this->processSections = $processSections;
        parent::__construct($path, self::FILE_EXTENSION);
    }

    /**
     * @inheritdoc
     */
    protected function parse($file)
    {
        return parse_ini_file($file, $this->processSections);
    }

}