<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse(string $content, string $extension): object
{
    switch ($extension) {
        case 'json':
            return json_decode($content);
        case 'yaml':
        case 'yml':
            return Yaml::parse($content, Yaml::PARSE_OBJECT_FOR_MAP);
        default:
            throw new \Exception('Wrong type of file');
    }
}
