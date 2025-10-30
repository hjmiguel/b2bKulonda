<?php

namespace App\Helpers;

class UnitHelper
{
    /**
     * Obter todas as unidades de medida
     */
    public static function getAllUnits()
    {
        return config('units.units', []);
    }

    /**
     * Obter unidades formatadas para dropdown/select
     * Retorna array [code => name_pt]
     */
    public static function getUnitsForDropdown()
    {
        return collect(config('units.units', []))
            ->sortBy('priority')
            ->pluck('name_pt', 'code')
            ->toArray();
    }

    /**
     * Obter unidades por categoria
     */
    public static function getUnitsByCategory($category)
    {
        return collect(config('units.units', []))
            ->filter(function($unit) use ($category) {
                return $unit['category'] === $category;
            })
            ->sortBy('priority')
            ->values()
            ->toArray();
    }

    /**
     * Obter informações de uma unidade específica pelo código
     */
    public static function getUnitByCode($code)
    {
        return collect(config('units.units', []))
            ->firstWhere('code', $code);
    }

    /**
     * Obter nome da unidade pelo código
     */
    public static function getUnitName($code, $lang = 'pt')
    {
        $unit = self::getUnitByCode($code);
        
        if (!$unit) {
            return $code;
        }

        return $lang === 'en' ? $unit['name_en'] : $unit['name_pt'];
    }

    /**
     * Obter símbolo da unidade pelo código
     */
    public static function getUnitSymbol($code)
    {
        $unit = self::getUnitByCode($code);
        return $unit ? $unit['symbol'] : $code;
    }

    /**
     * Formatar valor com unidade
     * Ex: formatValue(5, 'kg') => '5 kg'
     */
    public static function formatValue($value, $unitCode)
    {
        $symbol = self::getUnitSymbol($unitCode);
        return $value . ' ' . $symbol;
    }
}
