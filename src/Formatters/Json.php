<?php

namespace Differ\Formatters\Json;

function render(array $diffTree): string
{
    $result = json_encode($diffTree);
    if (is_string($result)) {
        return $result;
    } else {
        throw new \Exception("It's impossible to encode data to JSON format");
    }
}
