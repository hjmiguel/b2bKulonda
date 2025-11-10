<?php
/**
 * Remover Produtos CUCA Duplicados
 * Manter apenas os produtos originais (IDs 22-68)
 * Remover duplicatas (IDs 69-115)
 */

require __DIR__ . '/../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n";
echo "╔════════════════════════════════════════════════════════╗\n";
echo "║   REMOVER PRODUTOS DUPLICADOS - CUCA                  ║\n";
echo "╚════════════════════════════════════════════════════════╝\n";
echo "\n";

// IDs dos produtos duplicados a serem removidos (69-115)
$duplicateIds = range(69, 115);

echo "🗑️  Produtos a remover: " . count($duplicateIds) . "\n";
echo "   IDs: 69 até 115\n\n";

echo "1️⃣  Removendo traduções dos produtos duplicados...\n";
$translations = DB::table('product_translations')
    ->whereIn('product_id', $duplicateIds)
    ->delete();
echo "   ✓ $translations traduções removidas\n\n";

echo "2️⃣  Removendo stocks dos produtos duplicados...\n";
$stocks = DB::table('product_stocks')
    ->whereIn('product_id', $duplicateIds)
    ->delete();
echo "   ✓ $stocks stocks removidos\n\n";

echo "3️⃣  Removendo produtos duplicados...\n";
$products = DB::table('products')
    ->whereIn('id', $duplicateIds)
    ->delete();
echo "   ✓ $products produtos removidos\n\n";

// Verificar resultado final
echo "╔════════════════════════════════════════════════════════╗\n";
echo "║   VERIFICAÇÃO FINAL                                   ║\n";
echo "╚════════════════════════════════════════════════════════╝\n";
echo "\n";

$remaining = DB::table('products')->where('brand_id', 24)->count();
echo "📦 Total de produtos CUCA restantes: $remaining\n\n";

// Verificar duplicatas
$allCuca = DB::table('products')
    ->where('brand_id', 24)
    ->get(['id', 'slug']);

$grouped = [];
foreach ($allCuca as $product) {
    if (!isset($grouped[$product->slug])) {
        $grouped[$product->slug] = [];
    }
    $grouped[$product->slug][] = $product->id;
}

$duplicates = 0;
foreach ($grouped as $slug => $ids) {
    if (count($ids) > 1) {
        $duplicates++;
    }
}

echo "🔍 Slugs duplicados restantes: $duplicates\n\n";

if ($duplicates == 0) {
    echo "✅ Sucesso! Não há mais duplicatas.\n";
} else {
    echo "⚠️  Ainda existem duplicatas.\n";
}

echo "\n🔗 Teste agora: https://app.kulonda.ao/admin/products/admin/115/edit?lang=pt\n";
echo "   (Este link agora deve retornar 404 - produto não existe)\n\n";
