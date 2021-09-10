<?php

namespace Differ\Formatters\Json;

function render(array $diffTree): string
{
    return json_encode($diffTree);
}
