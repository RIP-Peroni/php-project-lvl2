<?php

namespace Differ\Formatters;

function format(array $diffTree, string $format): string
{
    switch ($format) {
        case 'stylish':
            return Stylish\render($diffTree);
        case 'plain':
            return Plain\render($diffTree);
        case 'json':
            return Json\render($diffTree);
        default:
            throw new \Exception("Wrong format {$format}!");
    }
}
