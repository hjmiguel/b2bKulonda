<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitTranslation extends Model
{
    protected $fillable = [
        'unit_id',
        'name',
        'description',
        'lang'
    ];

    /**
     * Relationship with Unit
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
