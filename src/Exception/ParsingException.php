<?php

namespace App\Exception;

class ParsingException extends AbstractAppException
{

    const PARSE_FILE_ERROR = 2001;
    const NO_PARSER_FOUND = 2002;
    const FAILED_TO_PARSE_INPUT = 2003;

    protected static array $messages = [
        self::PARSE_FILE_ERROR => 'Could not parse file.',
        self::NO_PARSER_FOUND => 'No parser found for provided content.',
        self::FAILED_TO_PARSE_INPUT => 'Parser failed to process content.',
    ];
}
