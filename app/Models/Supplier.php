<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [ 'name', 'active' ];

    protected $casts = [ 'active' => 'boolean' ];

    public function columnMappings(): HasMany
    {
        return $this->hasMany(CsvMapping::class);
    }

    public function outputColumns(): HasMany
    {
        return $this->hasMany(OutputColumn::class);
    }

    public function processingRules(): HasMany
    {
        return $this->hasMany(ProcessingRule::class);
    }
}
