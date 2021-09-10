<?php

namespace Differ\Differ;

use function Differ\Parsers\parse;
use function Differ\Formatters\format;

function parseJson(string $content): object
{
    return json_decode($content);
}

function genDiff(string $pathToFile1, string $pathToFile2, string $format = 'stylish'): string
{
    $structure1 = parse(getFileContent($pathToFile1), getFileType($pathToFile1));
    $structure2 = parse(getFileContent($pathToFile2), getFileType($pathToFile2));
    $diffTree = makeDiffTree($structure1, $structure2);
    return format($diffTree, $format);
}

function makeDiffTree(object $structure1, object $structure2): array
{
    $keys = array_keys(array_merge((array) $structure1, (array) $structure2));
    sort($keys);
    return array_map(
        function ($key) use ($structure1, $structure2) {
            $oldValue = $structure1->$key ?? null;
            $newValue = $structure2->$key ?? null;
            if (is_object($oldValue) && is_object($newValue)) {
                return [
                    'key' => $key,
                    'type' => 'nested',
                    'children' => makeDiffTree($structure1->$key, $structure2->$key)
                ];
            } elseif (!property_exists($structure2, $key)) {
                $type = 'removed';
            } elseif (!property_exists($structure1, $key)) {
                $type = 'added';
            } elseif ($oldValue !== $newValue) {
                $type = 'modified';
            } else {
                $type = 'unmodified';
            }

            return [
                'key' => $key,
                'type' => $type,
                'oldValue' => $oldValue,
                'newValue' => $newValue
            ];
        },
        $keys
    );
}

function getFileContent(string $path): string
{
    return file_get_contents($path);
}

function getFileType(string $path): string
{
    return pathinfo($path, PATHINFO_EXTENSION);
}
