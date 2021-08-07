<?php

namespace App\Parser\FormatParser;

use App\Parser\ConfigInterface;
use App\Exception\ParsingException;

interface FormatParserInterface
{

    /**
     * Parses passed content and returns instance of ConfigInterface
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
