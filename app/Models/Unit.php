<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;
use App;

class Unit extends Model
{
    use PreventDemoModeChanges;

    protected $fillable = [
        'name',
        'symbol',
        'type',
        'base_conversion_factor',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'base_conversion_factor' => 'decimal:4'
    ];

    protected $with = ['unit_translations'];

    /**
     * Get translation for a specific field
     */
    public function getTranslation($field = '', $lang = false)
    {
        $lang = $lang == false ? App::getLocale() : $lang;
        $unit_translation = $this->unit_translations->where('lang', $lang)->first();
        return $unit_translation != null ? $unit_translation->$field : $this->$field;
    }

    /**
     * Relationship with translations
     */
    public function unit_translations()
    {
        return $this->hasMany(UnitTranslation::class);
    }

    /**
     * Get all active units
     */
    public static function getActiveUnits()
    {
        return self::where('is_active', true)
                   ->orderBy('sort_order')
                   ->orderBy('name')
                   ->get();
    }

    /**
     * Get units by type
     */
    public static function getUnitsByType($type)
    {
        return self::where('type', $type)
                   ->where('is_active', true)
                   ->orderBy('sort_order')
                   ->orderBy('name')
                   ->get();
    }

    /**
     * Convert quantity from one unit to another
     */
    public static function convertQuantity($quantity, $fromUnitId, $toUnitId)
    {
        if ($fromUnitId == $toUnitId) {
            return $quantity;
        }

        $fromUnit = self::find($fromUnitId);
        $toUnit = self::find($toUnitId);

        if (!$fromUnit || !$toUnit) {
            return $quantity;
        }

        // Convert to base unit first, then to target unit
        $baseQuantity = $quantity * $fromUnit->base_conversion_factor;
        return $baseQuantity / $toUnit->base_conversion_factor;
    }
}
