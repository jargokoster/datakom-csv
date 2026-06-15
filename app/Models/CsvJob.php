<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CsvJob extends Model
{
    protected $fillable = [ 'supplier_id', 'original_filename', 'export_path', 'processed_rows' ];

    protected $casts = [ 'processed_rows' => 'array' ];
}
