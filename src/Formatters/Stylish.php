<?php

namespace Differ\Formatters\Stylish;

function render(array $diffTree): string
{
    $tempResult = makeStylish($diffTree);
    return "{\n" . "{$tempResult}" . "\n}";
}

function makeStylish(array $diffTree, int $depth = 1): string
{
    $result = array_map(
        function ($node) use ($depth) {
            $type = $node['type'];
            $key = $node['key'];
            $indent = makeIndent($depth);
            $smallIndent = makeIndent($depth, 2);

            switch ($type) {
                case 'nested':
                    return
                        "{$indent}{$key}: {" . "\n" . makeStylish($node['children'], $depth + 1) . "\n" . $indent . "}";
                case 'unmodified':
                    $value = stringify($node['oldValue'], $depth);
                    return "{$indent}{$key}: {$value}";
                case 'modified':
                    $oldValue = stringify($node['oldValue'], $depth);
                    $newValue = stringify($node['newValue'], $depth);
                    return "{$smallIndent}- {$key}: {$oldValue}\n"
                        . "{$smallIndent}+ {$key}: {$newValue}";
                case 'added':
                    $value = stringify($node['newValue'], $depth);
                    return "{$smallIndent}+ {$key}: {$value}";
                case 'removed':
                    $value = stringify($node['oldValue'], $depth);
                    return "{$smallIndent}- {$key}: {$value}";
                default:
                    throw new \Exception('Wrong node type');
            }
        },
        $diffTree
    );
    return implode("\n", $result);
}

function stringify(mixed $value, int $depth): string
{
    if (is_bool($value)) {
        return ($value) ? 'true' : 'false';
    }
    if (is_null($value)) {
        return 'null';
    }
    if (!is_object($value)) {
        return (string) $value;
    }
    $keys = array_keys(get_object_vars($value));
    $result = array_map(
        function ($key) use ($value, $depth) {
            $indent = makeIndent($depth + 1);
            return "{$indent}{$key}: " . stringify($value->$key, $depth + 1);
        },
        $keys
    );
    $indentEnd = makeIndent($depth);
    return "{\n" . implode("\n", $result) . "\n{$indentEnd}}";
}


function makeIndent(int $depth = 1, int $shift = 0): string
{
    $baseSize = 4;
    return str_repeat(' ', $baseSize * $depth - $shift);
}
