<?php

namespace App\Services\Csv\Rules;

final class RemoveRule implements CsvRule
{
    public function apply(array $row, array $config, string $column): array
    {
        if (isset($row[$column])) {
            unset($row[$column]);
        }

        return $row;
    }
}
