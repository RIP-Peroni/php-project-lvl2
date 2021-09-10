<?php

namespace Differ\Formatters\Plain;

function render(array $diffTree): string
{
    $result = makePlain($diffTree);
    return $result . "\n";
}

function makePlain(array $diffTree, mixed $path = ''): string
{
    $result = array_map(
        function ($node) use ($path) {
            $type = $node['type'];
            $key = $node['key'];
            switch ($type) {
                case 'nested':
                    $nestedPath = "{$path}{$key}.";
                    return makePlain($node['children'], $nestedPath);
                case 'modified':
                    $oldValue = stringify($node['oldValue']);
                    $newValue = stringify($node['newValue']);
                    return "Property '{$path}{$key}' was updated. From {$oldValue} to {$newValue}";
                case 'added':
                    $value = stringify($node['newValue']);
                    return "Property '{$path}{$key}' was added with value: {$value}";
                case 'removed':
                    $value = stringify($node['oldValue']);
                    return "Property '{$path}{$key}' was removed";
                case 'unmodified':
                    break;
                default:
                    throw new \Exception('Wrong node type');
            }
        },
        $diffTree
    );
    $filteredResult = array_filter($result, fn($node) => !empty($node));
    return implode("\n", $filteredResult);
}

function stringify(mixed $value): string
{
    if (is_bool($value)) {
        return ($value) ? 'true' : 'false';
    }
    if (is_null($value)) {
        return 'null';
    }
    if (!is_object($value)) {
        return (string) "'$value'";
    }
    return "[complex value]";
}
