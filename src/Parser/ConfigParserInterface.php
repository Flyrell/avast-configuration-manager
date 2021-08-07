<?php

namespace App\Parser;

interface ConfigParserInterface
{

    /**
     * Reads file contents in specified $filepath, parses it and converts to ConfigInterface
     *
     * @param string $filepath
     * @return ConfigInterface
     */
    public function parseFile(string $filepath): ConfigInterface;

    /**
     * Parses passed string and converts to ConfigInterface
     *
     * @param string $content
     * @return ConfigInterface
     */
    public function parseString(string $content): ConfigInterface;
}
