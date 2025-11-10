<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UnitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        
        $units = [
            // Unidades de Contagem
            [
                'id' => 1,
                'name' => 'Unidade',
                'symbol' => 'Un',
                'type' => 'count',
                'base_conversion_factor' => 1.0000,
                'is_active' => true,
                'sort_order' => 1,
                'translations' => [
                    ['name' => 'Unidade', 'description' => 'Unidade individual', 'lang' => 'pt'],
                    ['name' => 'Unit', 'description' => 'Individual unit', 'lang' => 'en'],
                ]
            ],
            [
                'id' => 2,
                'name' => 'Caixa',
                'symbol' => 'Cx',
                'type' => 'count',
                'base_conversion_factor' => 1.0000,
                'is_active' => true,
                'sort_order' => 2,
                'translations' => [
                    ['name' => 'Caixa', 'description' => 'Caixa de produtos', 'lang' => 'pt'],
                    ['name' => 'Box', 'description' => 'Box of products', 'lang' => 'en'],
                ]
            ],
            [
                'id' => 3,
                'name' => 'Pacote',
                'symbol' => 'Pct',
                'type' => 'count',
                'base_conversion_factor' => 1.0000,
                'is_active' => true,
                'sort_order' => 3,
                'translations' => [
                    ['name' => 'Pacote', 'description' => 'Pacote de produtos', 'lang' => 'pt'],
                    ['name' => 'Package', 'description' => 'Package of products', 'lang' => 'en'],
                ]
            ],
            [
                'id' => 4,
                'name' => 'Fardo',
                'symbol' => 'Fd',
                'type' => 'count',
                'base_conversion_factor' => 1.0000,
                'is_active' => true,
                'sort_order' => 4,
                'translations' => [
                    ['name' => 'Fardo', 'description' => 'Fardo de produtos', 'lang' => 'pt'],
                    ['name' => 'Bale', 'description' => 'Bale of products', 'lang' => 'en'],
                ]
            ],
            [
                'id' => 5,
                'name' => 'Engradado',
                'symbol' => 'Eng',
                'type' => 'count',
                'base_conversion_factor' => 1.0000,
                'is_active' => true,
                'sort_order' => 5,
                'translations' => [
                    ['name' => 'Engradado', 'description' => 'Engradado de produtos', 'lang' => 'pt'],
                    ['name' => 'Crate', 'description' => 'Crate of products', 'lang' => 'en'],
                ]
            ],
            [
                'id' => 6,
                'name' => 'Palete',
                'symbol' => 'Pallet',
                'type' => 'count',
                'base_conversion_factor' => 1.0000,
                'is_active' => true,
                'sort_order' => 6,
                'translations' => [
                    ['name' => 'Palete', 'description' => 'Palete de produtos', 'lang' => 'pt'],
                    ['name' => 'Pallet', 'description' => 'Pallet of products', 'lang' => 'en'],
                ]
            ],
            [
                'id' => 7,
                'name' => 'Dúzia',
                'symbol' => 'Dz',
                'type' => 'count',
                'base_conversion_factor' => 12.0000,
                'is_active' => true,
                'sort_order' => 7,
                'translations' => [
                    ['name' => 'Dúzia', 'description' => 'Conjunto de 12 unidades', 'lang' => 'pt'],
                    ['name' => 'Dozen', 'description' => 'Set of 12 units', 'lang' => 'en'],
                ]
            ],
            
            // Unidades de Peso
            [
                'id' => 8,
                'name' => 'Quilograma',
                'symbol' => 'Kg',
                'type' => 'weight',
                'base_conversion_factor' => 1.0000,
                'is_active' => true,
                'sort_order' => 10,
                'translations' => [
                    ['name' => 'Quilograma', 'description' => 'Unidade de peso - 1000 gramas', 'lang' => 'pt'],
                    ['name' => 'Kilogram', 'description' => 'Weight unit - 1000 grams', 'lang' => 'en'],
                ]
            ],
            [
                'id' => 9,
                'name' => 'Grama',
                'symbol' => 'g',
                'type' => 'weight',
                'base_conversion_factor' => 0.0010,
                'is_active' => true,
                'sort_order' => 11,
                'translations' => [
                    ['name' => 'Grama', 'description' => 'Unidade de peso - 0.001 quilograma', 'lang' => 'pt'],
                    ['name' => 'Gram', 'description' => 'Weight unit - 0.001 kilogram', 'lang' => 'en'],
                ]
            ],
            [
                'id' => 10,
                'name' => 'Tonelada',
                'symbol' => 'Ton',
                'type' => 'weight',
                'base_conversion_factor' => 1000.0000,
                'is_active' => true,
                'sort_order' => 12,
                'translations' => [
                    ['name' => 'Tonelada', 'description' => 'Unidade de peso - 1000 quilogramas', 'lang' => 'pt'],
                    ['name' => 'Ton', 'description' => 'Weight unit - 1000 kilograms', 'lang' => 'en'],
                ]
            ],
            
            // Unidades de Volume
            [
                'id' => 11,
                'name' => 'Litro',
                'symbol' => 'L',
                'type' => 'volume',
                'base_conversion_factor' => 1.0000,
                'is_active' => true,
                'sort_order' => 20,
                'translations' => [
                    ['name' => 'Litro', 'description' => 'Unidade de volume', 'lang' => 'pt'],
                    ['name' => 'Liter', 'description' => 'Volume unit', 'lang' => 'en'],
                ]
            ],
            [
                'id' => 12,
                'name' => 'Mililitro',
                'symbol' => 'ml',
                'type' => 'volume',
                'base_conversion_factor' => 0.0010,
                'is_active' => true,
                'sort_order' => 21,
                'translations' => [
                    ['name' => 'Mililitro', 'description' => 'Unidade de volume - 0.001 litro', 'lang' => 'pt'],
                    ['name' => 'Milliliter', 'description' => 'Volume unit - 0.001 liter', 'lang' => 'en'],
                ]
            ],
            [
                'id' => 13,
                'name' => 'Garrafa',
                'symbol' => 'Gf',
                'type' => 'volume',
                'base_conversion_factor' => 1.0000,
                'is_active' => true,
                'sort_order' => 22,
                'translations' => [
                    ['name' => 'Garrafa', 'description' => 'Garrafa de líquido', 'lang' => 'pt'],
                    ['name' => 'Bottle', 'description' => 'Bottle of liquid', 'lang' => 'en'],
                ]
            ],
            [
                'id' => 14,
                'name' => 'Barril',
                'symbol' => 'Barril',
                'type' => 'volume',
                'base_conversion_factor' => 50.0000,
                'is_active' => true,
                'sort_order' => 23,
                'translations' => [
                    ['name' => 'Barril', 'description' => 'Barril de líquido (aprox. 50 litros)', 'lang' => 'pt'],
                    ['name' => 'Barrel', 'description' => 'Barrel of liquid (approx. 50 liters)', 'lang' => 'en'],
                ]
            ],
            
            // Unidades Mistas
            [
                'id' => 15,
                'name' => 'Quilos por Caixa',
                'symbol' => 'Kg/Cx',
                'type' => 'mixed',
                'base_conversion_factor' => 1.0000,
                'is_active' => true,
                'sort_order' => 30,
                'translations' => [
                    ['name' => 'Quilos por Caixa', 'description' => 'Peso total da caixa em quilogramas', 'lang' => 'pt'],
                    ['name' => 'Kilos per Box', 'description' => 'Total weight of box in kilograms', 'lang' => 'en'],
                ]
            ],
            [
                'id' => 16,
                'name' => 'Unidades por Caixa',
                'symbol' => 'Un/Cx',
                'type' => 'mixed',
                'base_conversion_factor' => 1.0000,
                'is_active' => true,
                'sort_order' => 31,
                'translations' => [
                    ['name' => 'Unidades por Caixa', 'description' => 'Quantidade de unidades na caixa', 'lang' => 'pt'],
                    ['name' => 'Units per Box', 'description' => 'Number of units in box', 'lang' => 'en'],
                ]
            ],
            [
                'id' => 17,
                'name' => 'Litros por Caixa',
                'symbol' => 'L/Cx',
                'type' => 'mixed',
                'base_conversion_factor' => 1.0000,
                'is_active' => true,
                'sort_order' => 32,
                'translations' => [
                    ['name' => 'Litros por Caixa', 'description' => 'Volume total da caixa em litros', 'lang' => 'pt'],
                    ['name' => 'Liters per Box', 'description' => 'Total volume of box in liters', 'lang' => 'en'],
                ]
            ],
        ];

        // Insert units
        foreach ($units as $unitData) {
            $translations = $unitData['translations'];
            unset($unitData['translations']);
            
            $unitData['created_at'] = $now;
            $unitData['updated_at'] = $now;
            
            DB::table('units')->insert($unitData);
            
            // Insert translations
            foreach ($translations as $translation) {
                DB::table('unit_translations')->insert([
                    'unit_id' => $unitData['id'],
                    'name' => $translation['name'],
                    'description' => $translation['description'],
                    'lang' => $translation['lang'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
        
        $this->command->info('✓ Unidades HORECA criadas com sucesso!');
    }
}
