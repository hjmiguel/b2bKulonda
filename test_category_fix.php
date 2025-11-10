<?php
/**
 * Teste: Verificar se pluck retorna IDs corretos
 */

require __DIR__./vendor/autoload.php;

$app = require_once __DIR__./bootstrap/app.php;
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Pegar um produto com categorias
$product = App\Models\Product::with("categories")->whereHas("categories")->first();

if (!$product) {
    echo "❌ Nenhum produto com categorias encontrado!\n";
    exit;
}

echo "=== TESTE DE CATEGORIAS - Produto #{$product->id} ===\n\n";

// MÉTODO ATUAL (pode estar errado)
$old_method = $product->categories()->pluck('category_id')->toArray();
echo "1. Método ATUAL (->categories()->pluck('category_id')):\n";
print_r($old_method);
echo "\n";

// MÉTODO CORRETO
$new_method = $product->categories->pluck('id')->toArray();
echo "2. Método CORRETO (->categories->pluck('id')):\n";
print_r($new_method);
echo "\n";

// Verificar tabela pivot diretamente
$pivot_data = DB::table('product_categories')
    ->where('product_id', $product->id)
    ->get();
    
echo "3. Dados diretos da tabela product_categories:\n";
foreach ($pivot_data as $row) {
    echo "   - product_id: {$row->product_id}, category_id: {$row->category_id}\n";
}
echo "\n";

// Testar se categorias existem
echo "4. Nomes das categorias associadas:\n";
foreach ($product->categories as $cat) {
    echo "   - ID {$cat->id}: {$cat->name}\n";
}

echo "\n✅ Teste concluído!\n";
