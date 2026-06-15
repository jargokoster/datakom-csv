<?php

namespace App\Services\Csv\Rules;

use InvalidArgumentException;

final class RuleFactory
{
    public function run(string $type): CsvRule
    {
        return match($type) {
            'multiply' => new MultiplyRule(),
            'remove' => new RemoveRule(),
            'regexp' => new RegexpRule(),
            default => throw new InvalidArgumentException("Unknown rule type:".$type),
        };
    }
}
