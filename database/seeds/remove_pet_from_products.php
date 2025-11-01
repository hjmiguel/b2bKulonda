<?php
/**
 * Remover palavra "PET" dos nomes dos produtos CUCA
 */

require __DIR__ . '/../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "\n";
echo "╔════════════════════════════════════════════════════════╗\n";
echo "║   REMOVER 'PET' DOS PRODUTOS CUCA                     ║\n";
echo "╚════════════════════════════════════════════════════════╝\n";
echo "\n";

$now = Carbon::now();

// Buscar produtos com "PET" no nome
$products = DB::table('products')
    ->where('brand_id', 24)
    ->where('name', 'LIKE', '%PET%')
    ->get(['id', 'name', 'slug']);

echo "📦 Produtos encontrados com 'PET': " . count($products) . "\n\n";

$updated = 0;

foreach ($products as $product) {
    $oldName = $product->name;
    $oldSlug = $product->slug;

    // Remover "PET" e espaços extras
    $newName = str_replace('PET ', '', $oldName);
    $newName = str_replace(' PET', '', $newName);
    $newName = trim($newName);

    // Gerar novo slug
    $newSlug = strtolower($newName);
    $newSlug = preg_replace('/[^a-z0-9]+/', '-', $newSlug);
    $newSlug = trim($newSlug, '-');

    // Atualizar produto
    DB::table('products')
        ->where('id', $product->id)
        ->update([
            'name' => $newName,
            'slug' => $newSlug,
            'updated_at' => $now,
        ]);

    // Atualizar traduções
    DB::table('product_translations')
        ->where('product_id', $product->id)
        ->update([
            'name' => $newName,
            'updated_at' => $now,
        ]);

    $updated++;

    echo "  [$product->id] ANTES: $oldName\n";
    echo "  [$product->id] DEPOIS: $newName\n\n";
}

echo "╔════════════════════════════════════════════════════════╗\n";
echo "║   CONCLUÍDO                                           ║\n";
echo "╚════════════════════════════════════════════════════════╝\n";
echo "\n";
echo "✅ Total atualizado: $updated produtos\n\n";
