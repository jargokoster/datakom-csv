<?php

namespace App\Services\Csv\Rules;

final class MultiplyRule implements CsvRule
{
    public function apply(array $row, array $config, string $column): array
    {
        if (isset($row[$column]) && is_numeric($row[$column])) {
            $row[$column] = (string) ((float)$row[$column] * (float)($config['factor'] ?? 1));
        }

        return $row;
    }
}
