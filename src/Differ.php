<?php

namespace Differ\Differ;

use function Differ\Parsers\parse;

function parseJson(string $content): object
{
    return json_decode($content);
}

function generateDiff(string $pathToFile1, string $pathToFile2): string
{
    $structure1 = parse(getFileContent($pathToFile1), getFileType($pathToFile1));
    $structure2 = parse(getFileContent($pathToFile2), getFileType($pathToFile2));
    $diffTree = getDiffTree($structure1, $structure2);
    return format($diffTree);
}

function getDiffTree(object $structure1, object $structure2): array
{
    $keys = array_keys(array_merge((array) $structure1, (array) $structure2));
    sort($keys);
    return array_map(
        function ($key) use ($structure1, $structure2) {
            $oldValue = $structure1->$key ?? null;
            $newValue = $structure2->$key ?? null;

            if (!property_exists($structure2, $key)) {
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

function format(array $diffTree): string
{
    $result = stylize($diffTree);
    return "{\n" . "{$result}" . "\n}\n";
}

function stylize(array $diffTree): string
{
    $result = array_map(
        function ($node) {
            $type = $node['type'];
            $key = $node['key'];
            $bigIndent = str_repeat(' ', 4);
            $smallIndent = str_repeat(' ', 2);

            switch ($type) {
                case 'unmodified':
                    $value = toString($node['oldValue']);
                    return "{$bigIndent}{$key}: {$value}";
                case 'modified':
                    $oldValue = toString($node['oldValue']);
                    $newValue = toString($node['newValue']);
                    return "{$smallIndent}- {$key}: {$oldValue}\n"
                        . "{$smallIndent}+ {$key}: {$newValue}";
                case 'added':
                    $value = toString($node['newValue']);
                    return "{$smallIndent}+ {$key}: {$value}";
                case 'removed':
                    $value = toString($node['oldValue']);
                    return "{$smallIndent}- {$key}: {$value}";
                default:
                    throw new \Exception('Wrong node type');
            }
        },
        $diffTree
    );
    return implode("\n", $result);
}

function toString(mixed $value): string
{
    if (is_bool($value)) {
        return ($value) ? 'true' : 'false';
    }
    if (is_null($value)) {
        return 'null';
    }
    return $value;
}

function getFileContent(string $path): string
{
	return file_get_contents($path);
}

function getFileType(string $path): string
{
	return pathinfo($path, PATHINFO_EXTENSION);
}