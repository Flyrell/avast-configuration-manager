<?php

namespace App\Parser\Config\FormatParser;

use App\Exception\ParsingException;
use App\Parser\Config\ConfigInterface;

interface FormatParserInterface
{

    /**
     * Parses the passed content and returns instance of ConfigInterface
     *
     * @param string $content
     * @return ConfigInterface
     * @throws ParsingException
     */
    public function parse(string $content): ConfigInterface;

    /**
     * Determines whether parser supports provided content
     *
     * @param string $content
     * @return bool
     */
    public function supports(string $content): bool;
}
