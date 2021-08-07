<?php

namespace App\Parser;

use App\Exception\ParsingException;

interface ConfigParserInterface
{

    /**
     * Reads file contents in specified filepath, parses it and converts to ConfigInterface.
     *
     * @param string $filepath
     * @return ConfigInterface
     * @throws ParsingException
     */
    public function parseFile(string $filepath): ConfigInterface;

    /**
     * Parses passed string and converts to ConfigInterface.
     *
     * @param string $content
     * @return ConfigInterface
     * @throws ParsingException
     */
    public function parseString(string $content): ConfigInterface;
}
