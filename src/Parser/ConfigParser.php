<?php

namespace App\Parser;

use App\Service\FileService;
use App\Exception\FileException;
use App\Exception\ParsingException;
use App\Parser\FormatParser\FormatParserInterface;

class ConfigParser implements ConfigParserInterface
{

    /**
     * @param FormatParserInterface[] $parsers
     * @param FileService $fileService
     */
    public function __construct(
        private array $parsers,
        private FileService $fileService,
    ) {}

    /**
     * @param string $filepath
     * @return ConfigInterface
     * @throws ParsingException
     */
    public function parseFile(string $filepath): ConfigInterface
    {
        try {
            $content = $this->fileService->read($filepath);
            return $this->parseString($content);
        } catch (FileException $e) {
            throw new ParsingException(ParsingException::PARSE_FILE_ERROR, $e);
        }
    }

    /**
     * @param string $content
     * @return ConfigInterface
     * @throws ParsingException
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
