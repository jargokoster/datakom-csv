<?php

namespace App\Services\Csv\Rules;

final class RegexpRule implements CsvRule
{
    public function apply(array $row, array $config, string $column): array
    {
        if (isset($row[$column])) {
            $row[$column] = preg_replace($config['pattern'], $config['replacement'] ?? '', (string)$row[$column]);
        }
        return $row;
    }
}
