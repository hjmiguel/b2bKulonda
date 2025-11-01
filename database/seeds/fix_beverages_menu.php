<?php
/**
 * Corrigir Menu de Bebidas
 * Fazer Bebidas Alcoólicas e Não Alcoólicas serem filhas de Bebidas
 */

require __DIR__ . '/../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "\n";
echo "╔════════════════════════════════════════════════════════╗\n";
echo "║   CORREÇÃO DO MENU - BEBIDAS                          ║\n";
echo "╚════════════════════════════════════════════════════════╝\n";
echo "\n";

$now = Carbon::now();

// Opção: Fazer "Bebidas Alcoólicas" e "Bebidas Não Alcoólicas"
// serem subcategorias de "Bebidas" (ID: 70)

echo "1️⃣  Atualizando estrutura do menu...\n\n";

// Reativar categoria Bebidas (ID: 70)
DB::table('categories')
    ->where('id', 70)
    ->update([
        'featured' => 1,
        'top' => 1,
        'updated_at' => $now,
    ]);

echo "   ✓ Categoria 'Bebidas' (ID: 70) reativada\n";

// Fazer "Bebidas Alcoólicas" ser filha de "Bebidas"
DB::table('categories')
    ->where('id', 132)
    ->update([
        'parent_id' => 70,
        'level' => 1,
        'updated_at' => $now,
    ]);

echo "   ✓ 'Bebidas Alcoólicas' → parent: 70 (Bebidas)\n";

// Fazer "Bebidas Não Alcoólicas" ser filha de "Bebidas"
DB::table('categories')
    ->where('id', 133)
    ->update([
        'parent_id' => 70,
        'level' => 1,
        'updated_at' => $now,
    ]);

echo "   ✓ 'Bebidas Não Alcoólicas' → parent: 70 (Bebidas)\n\n";

// Atualizar level das subcategorias (Cervejas, Vinhos, etc)
echo "2️⃣  Atualizando levels das subcategorias...\n\n";

// Subcategorias de Alcoólicas (agora level 2)
$alcoholicSubs = [72, 71, 73]; // Cervejas, Vinhos, Destilados
foreach ($alcoholicSubs as $catId) {
    DB::table('categories')
        ->where('id', $catId)
        ->update([
            'level' => 2,
            'updated_at' => $now,
        ]);
}
echo "   ✓ Subcategorias Alcoólicas → level: 2\n";

// Subcategorias de Não Alcoólicas (agora level 2)
$nonAlcoholicSubs = [74, 75, 76, 77]; // Água, Refrigerantes, Sucos, Café
foreach ($nonAlcoholicSubs as $catId) {
    DB::table('categories')
        ->where('id', $catId)
        ->update([
            'level' => 2,
            'updated_at' => $now,
        ]);
}
echo "   ✓ Subcategorias Não Alcoólicas → level: 2\n\n";

// Verificar estrutura final
echo "╔════════════════════════════════════════════════════════╗\n";
echo "║   ESTRUTURA FINAL                                     ║\n";
echo "╚════════════════════════════════════════════════════════╝\n";
echo "\n";

$bebidas = DB::table('categories')->where('id', 70)->first();
$children = DB::table('categories')->where('parent_id', 70)->get();

echo "📁 {$bebidas->name} (ID: 70) - Level: {$bebidas->level}\n";
foreach ($children as $child) {
    $grandchildren = DB::table('categories')->where('parent_id', $child->id)->get();
    $productCount = DB::table('products')->where('brand_id', 24)
        ->whereIn('category_id', $grandchildren->pluck('id'))
        ->count();

    echo "   ├── {$child->name} (ID: {$child->id}) - Level: {$child->level}\n";

    foreach ($grandchildren as $gc) {
        $gcProducts = DB::table('products')
            ->where('category_id', $gc->id)
            ->where('brand_id', 24)
            ->count();
        echo "   │   └── {$gc->name} (ID: {$gc->id}) - {$gcProducts} produtos CUCA\n";
    }
}

echo "\n";
echo "✅ Menu corrigido com sucesso!\n";
echo "🔗 https://app.kulonda.ao/category/bebidas\n";
echo "\n";
