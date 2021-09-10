<?php

namespace Differ\Differ;

use function Differ\Parsers\parse;
use function Differ\Formatters\format;
use function Functional\sort;

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
    $sortedKeys = sort($keys, fn($a, $b) => $a <=> $b);
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
                return ['key' => $key, 'type' => 'removed', 'oldValue' => $oldValue];
            } elseif (!property_exists($structure1, $key)) {
                return ['key' => $key, 'type' => 'added', 'newValue' => $newValue];
            } elseif ($oldValue !== $newValue) {
                return ['key' => $key, 'type' => 'modified', 'oldValue' => $oldValue, 'newValue' => $newValue];
            } else {
                return ['key' => $key, 'type' => 'unmodified', 'oldValue' => $oldValue];
            }
        },
        $sortedKeys
    );
}

function getFileContent(string $path): string
{
    if (is_readable($path)) {
        $fileData = file_get_contents($path);
    } else {
        throw new \Exception("{$path} doesn't exist or doesn't readable");
    }

    if (is_string($fileData)) {
        return $fileData;
    } else {
        throw new \Exception("{$path} is not in string format");
    }
}

function getFileType(string $path): string
{
    return pathinfo($path, PATHINFO_EXTENSION);
}
