<?php

namespace App\Services\Csv;

use App\Models\Supplier;
use App\Services\Csv\Rules\RuleFactory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

//process CSV file and attach rules based on Supplier config
final class CsvProcessor
{
    public function __construct(private RuleFactory $ruleFactory)
    {
    }

    public function process(Supplier $supplier, UploadedFile $file, bool $saveFile = true): array
    {
        $fileHandle = fopen($file->getRealPath(), 'r');
        if ($fileHandle === false) {
            throw new RuntimeException("Could not open uploaded CSV file.");
        }

        $csvHeaders = fgetcsv($fileHandle);
        if ($csvHeaders === false) {
            throw new RuntimeException("Cannot read CSV column names.");
        }

        $rows = [];

        while (($row = fgetcsv($fileHandle)) !== false) {
            //check if row column count matches with $csvHeaders count, if not then skip the row
            if (count($csvHeaders) !== count($row)) {
                continue;
            }

            $rows[] = array_combine($csvHeaders, $row);
        }

        fclose($fileHandle);

        //map row columns to column mapping settings
        //if there is now mapping for specific column then take column name AS IS
        $csvMappings = $supplier->columnMappings()
                                    ->pluck('target_column', 'source_column')
                                    ->toArray();
        $rowsProcessed = array_map(function (array $row) use ($csvMappings) {
            $mapped = [];
            foreach ($row as $key => $value) {
                $mapped[$csvMappings[$key] ?? $key] = $value;
            }

            return $mapped;
        }, $rows);

        //run rules based on supplier rules settings
        $rules = $supplier->processingRules()
                            ->where('active', true)
                            ->orderBy('order')
                            ->get();

        foreach ($rules as $ruleModel) {
            $rule = $this->ruleFactory->run($ruleModel->type);

            $rowsProcessed = array_map(fn (array $row) => $rule->apply(
                $row,
                $ruleModel->config ?? [],
                $ruleModel->column_name
            ), $rowsProcessed);
        }

        //check output columns based on supplier
        $columns = $supplier->outputColumns()
                            ->where('active', true)
                            ->where('hidden', false)
                            ->orderBy('order')
                            ->pluck('name')
                            ->toArray();
        $rowsProcessed = array_map(function (array $row) use ($columns) {
            return array_intersect_key($row, array_flip($columns));
        }, $rowsProcessed);


        $exportPath = null;
        if ($saveFile) {
            $exportPath = 'exports/processed_' . now()->format('Ymd_His') . '.csv';
            $this->writeCsv($exportPath, $columns, $rowsProcessed);
        }
        return [
            'rows' => $rowsProcessed,
            'export_path' => $exportPath
        ];
    }

    //write/create CSV file based on output column info
    private function writeCsv(string $path, array $headers, array $rows): void
    {
        $tmpFile = fopen('php://temp', 'r+');

        //write column names
        fputcsv($tmpFile, $headers);

        //write only row columns that are in output column list
        foreach ($rows as $row) {
            fputcsv($tmpFile, array_map(fn ($header) => $row[$header] ?? '', $headers));
        }

        rewind($tmpFile);

        //save file to path $path and based on storage backend
        Storage::put($path, stream_get_contents($tmpFile));

        fclose($tmpFile);
    }
}
