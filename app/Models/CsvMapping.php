<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CsvMapping extends Model
{
    protected $fillable = [ 'supplier_id', 'source_column', 'target_column' ];
}
