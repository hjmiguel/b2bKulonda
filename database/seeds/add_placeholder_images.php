<?php
/**
 * Adicionar imagens placeholder aos produtos CUCA
 * Para que apareçam na listagem de categorias
 */

require __DIR__ . '/../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "\n";
echo "╔════════════════════════════════════════════════════════╗\n";
echo "║   ADICIONAR IMAGENS PLACEHOLDER - PRODUTOS CUCA       ║\n";
echo "╚════════════════════════════════════════════════════════╝\n";
echo "\n";

$now = Carbon::now();

// Usar imagem placeholder do sistema
$placeholder = 'frontend/images/placeholder.jpg';

// Atualizar todos os produtos CUCA sem imagem
$updated = DB::table('products')
    ->where('brand_id', 24)
    ->whereNull('thumbnail_img')
    ->update([
        'thumbnail_img' => $placeholder,
        'updated_at' => $now,
    ]);

echo "✓ Imagem placeholder adicionada a $updated produtos CUCA\n\n";

// Verificar resultado
echo "📊 Verificação:\n";
$withImage = DB::table('products')
    ->where('brand_id', 24)
    ->whereNotNull('thumbnail_img')
    ->count();

$withoutImage = DB::table('products')
    ->where('brand_id', 24)
    ->whereNull('thumbnail_img')
    ->count();

echo "   Com imagem: $withImage produtos\n";
echo "   Sem imagem: $withoutImage produtos\n\n";

// Listar alguns produtos
echo "📦 Primeiros 5 produtos CUCA:\n";
$products = DB::table('products')
    ->where('brand_id', 24)
    ->take(5)
    ->get(['id', 'name', 'thumbnail_img', 'category_id']);

foreach ($products as $p) {
    $hasImg = $p->thumbnail_img ? '✓' : '✗';
    echo "   $hasImg [$p->id] $p->name (Cat: $p->category_id)\n";
}

echo "\n✅ Processo concluído!\n";
echo "🔗 Teste: https://app.kulonda.ao/category/cervejas\n\n";
