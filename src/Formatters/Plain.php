<?php

namespace Differ\Formatters\Plain;

use function Functional\flatten;

function render(array $diffTree): string
{
    $result = makePlain($diffTree);
    return implode("\n", flatten($result));
}

function makePlain(array $diffTree, string $path = ''): array
{
    return array_map(
        function ($node) use ($path): mixed {
            $type = $node['type'];
            switch ($type) {
                case 'nested':
                    $nestedPath = "{$path}{$node['key']}.";
                    return makePlain($node['children'], $nestedPath);
                case 'modified':
                    $oldValue = stringify($node['oldValue']);
                    $newValue = stringify($node['newValue']);
                    return "Property '{$path}{$node['key']}' was updated. From {$oldValue} to {$newValue}";
                case 'added':
                    $value = stringify($node['newValue']);
                    return "Property '{$path}{$node['key']}' was added with value: {$value}";
                case 'removed':
                    $value = stringify($node['oldValue']);
                    return "Property '{$path}{$node['key']}' was removed";
                case 'unmodified':
                    return [];
                default:
                    throw new \Exception('Wrong node type');
            }
        },
        $diffTree
    );
}

function stringify(mixed $value): string|int
{
    if (is_bool($value)) {
        return ($value) ? 'true' : 'false';
    }
    if (is_null($value)) {
        return 'null';
    }
    if (is_int($value)) {
        return $value;
    }
    if (!is_object($value)) {
        return "'$value'";
    }
    return "[complex value]";
}
