<?php

namespace App\Services\Csv\Rules;

//interface to apply rule to a row
interface CsvRule
{
    public function apply(array $row, array $config, string $column): array;
}
