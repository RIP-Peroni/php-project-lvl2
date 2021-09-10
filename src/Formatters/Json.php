<?php

namespace Differ\Formatters\Json;

function render(array $diffTree): string
{
    return (string) json_encode($diffTree);
}
