<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessingRule extends Model
{
    protected $fillable = [ 'supplier_id', 'type', 'column_name', 'config', 'order', 'active' ];

    protected $casts = [ 'config' => 'array', 'active' => 'boolean' ];
}
