<?php
/**
 * Script de Reorganização de Categorias
 * Separar Bebidas Alcoólicas e Não Alcoólicas
 */

require __DIR__ . '/../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "\n";
echo "╔════════════════════════════════════════════════════════╗\n";
echo "║   REORGANIZAÇÃO DE CATEGORIAS - BEBIDAS               ║\n";
echo "║   Separar Alcoólicas e Não Alcoólicas                 ║\n";
echo "╚════════════════════════════════════════════════════════╝\n";
echo "\n";

$now = Carbon::now();

// 1. Criar categoria "Bebidas Alcoólicas"
echo "1️⃣  Criando categoria: Bebidas Alcoólicas...\n";

$alcoholicId = DB::table('categories')->insertGetId([
    'name' => 'Bebidas Alcoolicas',
    'slug' => 'bebidas-alcoolicas',
    'parent_id' => 0,
    'level' => 0,
    'commision_rate' => 0,
    'banner' => null,
    'icon' => null,
    'featured' => 1,
    'top' => 1,
    'digital' => 0,
    'created_at' => $now,
    'updated_at' => $now,
]);

echo "   ✓ Categoria criada: ID = $alcoholicId\n";

// Traduções PT
DB::table('category_translations')->insert([
    'category_id' => $alcoholicId,
    'name' => 'Bebidas Alcoólicas',
    'lang' => 'pt',
    'created_at' => $now,
    'updated_at' => $now,
]);

// Traduções EN
DB::table('category_translations')->insert([
    'category_id' => $alcoholicId,
    'name' => 'Alcoholic Beverages',
    'lang' => 'en',
    'created_at' => $now,
    'updated_at' => $now,
]);

echo "   ✓ Traduções criadas (PT/EN)\n\n";

// 2. Criar categoria "Bebidas Não Alcoólicas"
echo "2️⃣  Criando categoria: Bebidas Não Alcoólicas...\n";

$nonAlcoholicId = DB::table('categories')->insertGetId([
    'name' => 'Bebidas Nao Alcoolicas',
    'slug' => 'bebidas-nao-alcoolicas',
    'parent_id' => 0,
    'level' => 0,
    'commision_rate' => 0,
    'banner' => null,
    'icon' => null,
    'featured' => 1,
    'top' => 1,
    'digital' => 0,
    'created_at' => $now,
    'updated_at' => $now,
]);

echo "   ✓ Categoria criada: ID = $nonAlcoholicId\n";

// Traduções PT
DB::table('category_translations')->insert([
    'category_id' => $nonAlcoholicId,
    'name' => 'Bebidas Não Alcoólicas',
    'lang' => 'pt',
    'created_at' => $now,
    'updated_at' => $now,
]);

// Traduções EN
DB::table('category_translations')->insert([
    'category_id' => $nonAlcoholicId,
    'name' => 'Non-Alcoholic Beverages',
    'lang' => 'en',
    'created_at' => $now,
    'updated_at' => $now,
]);

echo "   ✓ Traduções criadas (PT/EN)\n\n";

// 3. Reorganizar subcategorias - ALCOÓLICAS
echo "3️⃣  Reorganizando subcategorias ALCOÓLICAS...\n";

$alcoholicCategories = [
    72 => 'Cervejas',
    71 => 'Vinhos',
    73 => 'Destilados',
];

foreach ($alcoholicCategories as $catId => $catName) {
    DB::table('categories')
        ->where('id', $catId)
        ->update([
            'parent_id' => $alcoholicId,
            'level' => 1,
            'updated_at' => $now,
        ]);
    echo "   ✓ $catName → parent: $alcoholicId (Bebidas Alcoólicas)\n";
}

echo "\n";

// 4. Reorganizar subcategorias - NÃO ALCOÓLICAS
echo "4️⃣  Reorganizando subcategorias NÃO ALCOÓLICAS...\n";

$nonAlcoholicCategories = [
    74 => 'Água',
    75 => 'Refrigerantes',
    76 => 'Sucos',
    77 => 'Café e Chá',
];

foreach ($nonAlcoholicCategories as $catId => $catName) {
    DB::table('categories')
        ->where('id', $catId)
        ->update([
            'parent_id' => $nonAlcoholicId,
            'level' => 1,
            'updated_at' => $now,
        ]);
    echo "   ✓ $catName → parent: $nonAlcoholicId (Bebidas Não Alcoólicas)\n";
}

echo "\n";

// 5. Verificar produtos CUCA
echo "5️⃣  Verificando distribuição dos produtos CUCA...\n";

$cervejas = DB::table('products')->where('category_id', 72)->where('brand_id', 24)->count();
$refrigerantes = DB::table('products')->where('category_id', 75)->where('brand_id', 24)->count();
$sumos = DB::table('products')->where('category_id', 76)->where('brand_id', 24)->count();

echo "   📊 Cervejas: $cervejas produtos\n";
echo "   📊 Refrigerantes: $refrigerantes produtos\n";
echo "   📊 Sumos: $sumos produtos\n";

$total = $cervejas + $refrigerantes + $sumos;
echo "   📊 Total: $total produtos CUCA\n\n";

// 6. Atualizar categoria "Bebidas" (ID 70) para não ser mais principal
echo "6️⃣  Atualizando categoria 'Bebidas' original...\n";

DB::table('categories')
    ->where('id', 70)
    ->update([
        'featured' => 0,
        'top' => 0,
        'updated_at' => $now,
    ]);

echo "   ✓ Categoria 'Bebidas' (ID: 70) desativada de destaque\n\n";

// Resumo final
echo "╔════════════════════════════════════════════════════════╗\n";
echo "║   REORGANIZAÇÃO CONCLUÍDA                             ║\n";
echo "╚════════════════════════════════════════════════════════╝\n";
echo "\n";
echo "✅ Nova estrutura:\n";
echo "\n";
echo "📁 Bebidas Alcoólicas (ID: $alcoholicId)\n";
echo "   ├── Cervejas (ID: 72) - $cervejas produtos\n";
echo "   ├── Vinhos (ID: 71)\n";
echo "   └── Destilados (ID: 73)\n";
echo "\n";
echo "📁 Bebidas Não Alcoólicas (ID: $nonAlcoholicId)\n";
echo "   ├── Água (ID: 74)\n";
echo "   ├── Refrigerantes (ID: 75) - $refrigerantes produtos\n";
echo "   ├── Sucos (ID: 76) - $sumos produtos\n";
echo "   └── Café e Chá (ID: 77)\n";
echo "\n";
echo "🔗 URLs:\n";
echo "   - Bebidas Alcoólicas: https://app.kulonda.ao/category/bebidas-alcoolicas\n";
echo "   - Bebidas Não Alcoólicas: https://app.kulonda.ao/category/bebidas-nao-alcoolicas\n";
echo "   - Cervejas CUCA: https://app.kulonda.ao/category/cervejas\n";
echo "\n";
