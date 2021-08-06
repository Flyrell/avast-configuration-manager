<?php

namespace App\Tests\Unit\Utils;

class ConfigParserUtils {

    public static string $supportedXMLContent = '<config><subdomains></subdomains><cookies></cookies></config>';
    public static string $unsupportedXMLContent = '<config><subdomains2></subdomains2><cookies></cookies></config>';
    public static string $wrongXMLContent = '<subdomains2></subdomains2><cookies></cookies></config>';
}
