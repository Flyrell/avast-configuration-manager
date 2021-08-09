<?php

namespace App\Parser;

use App\Service\FileService;
use App\Exception\FileException;
use App\Exception\ParsingException;
use App\Parser\FormatParser\FormatParserInterface;
use function is_null;

class ConfigParser implements ConfigParserInterface
{

    public function __construct(private array $parsers, private FileService $fileService) {}

    /**
     * @inheritDoc
     */
    public function parseFile(string $filepath): ConfigInterface
    {
        try {
            $content = $this->fileService->read($filepath);
            return $this->parseString($content);
        } catch (FileException $e) {
            throw new ParsingException(ParsingException::PARSE_FILE_ERROR, [], $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function parseString(string $content): ConfigInterface
    {
        $parser = $this->findParser($content);
        if (is_null($parser)) {
            throw new ParsingException(ParsingException::NO_PARSER_FOUND);
        }

        return $parser->parse($content);
    }

    /**
     * Iterates available parsers and returns the first supported one.
     * Returns NULL if the content is not supported by any available parsers.
     *
     * @param string $serializedConfig
     * @return ConfigInterface|null
     */
    private function findParser(string $serializedConfig): ?FormatParserInterface
    {
        foreach ($this->parsers as $parser) {
            if ($parser->supports($serializedConfig)) {
                return $parser;
            }
        }
        return null;
    }
}
