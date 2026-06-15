<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutputColumn extends Model
{
    protected $fillable = [ 'supplier_id', 'name', 'order', 'active', 'hidden' ];

    protected $casts = [ 'active' => 'boolean', 'hidden' => 'boolean' ];
}
