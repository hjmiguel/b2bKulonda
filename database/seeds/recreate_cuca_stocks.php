<?php
/**
 * Recriar stocks para todos os produtos CUCA
 * Stock inicial: 10 unidades
 */

require __DIR__ . '/../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "\n";
echo "╔════════════════════════════════════════════════════════╗\n";
echo "║   RECRIAR STOCKS - PRODUTOS CUCA (10 unidades)       ║\n";
echo "╚════════════════════════════════════════════════════════╝\n";
echo "\n";

$now = Carbon::now();
$stockQuantity = 10;

$products = DB::table('products')
    ->where('brand_id', 24)
    ->get(['id', 'name']);

echo "📦 Total de produtos CUCA: " . count($products) . "\n";
echo "📊 Stock inicial: $stockQuantity unidades\n\n";

$created = 0;

foreach ($products as $product) {
    $existingStock = DB::table('product_stocks')
        ->where('product_id', $product->id)
        ->count();

    if ($existingStock == 0) {
        DB::table('product_stocks')->insert([
            'product_id' => $product->id,
            'variant' => '',
            'sku' => 'CUCA-' . str_pad($product->id, 6, '0', STR_PAD_LEFT),
            'qty' => $stockQuantity,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('products')
            ->where('id', $product->id)
            ->update([
                'current_stock' => $stockQuantity,
                'updated_at' => $now,
            ]);

        $created++;
        echo "  ✓ [$product->id] $product->name - Stock: $stockQuantity\n";
    } else {
        echo "  ⏭️  [$product->id] $product->name - Stock já existe\n";
    }
}

echo "\n╔════════════════════════════════════════════════════════╗\n";
echo "║   CONCLUÍDO                                           ║\n";
echo "╚════════════════════════════════════════════════════════╝\n";
echo "\n";
echo "✅ Stocks criados: $created produtos\n\n";

$withStock = 0;
$withoutStock = 0;

foreach ($products as $product) {
    $stockCount = DB::table('product_stocks')->where('product_id', $product->id)->count();
    if ($stockCount > 0) {
        $withStock++;
    } else {
        $withoutStock++;
    }
}

echo "📊 VERIFICAÇÃO FINAL:\n";
echo "   ✅ Com stock: $withStock produtos\n";
echo "   ❌ Sem stock: $withoutStock produtos\n\n";

if ($withoutStock == 0) {
    echo "✅ Perfeito! Todos os produtos têm stock de $stockQuantity unidades.\n";
} else {
    echo "⚠️  Ainda há $withoutStock produtos sem stock.\n";
}

echo "\n🔗 Teste: https://app.kulonda.ao/admin/products/admin/68/edit?lang=pt\n\n";
