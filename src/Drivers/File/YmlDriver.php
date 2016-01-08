<?php

namespace Assertis\Configuration\Drivers\File;

use Exception;
use Symfony\Component\Yaml\Parser;

/**
 * @package Assertis\Configuration\Drivers\File
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
class YmlDriver extends AbstractFileDriver
{
    const FILE_EXTENSION = 'yml';
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->parser = new Parser();
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
        return $this->parser->parse(file_get_contents($file));
    }
}
