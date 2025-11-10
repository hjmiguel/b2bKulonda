<?php

require __DIR__."/vendor/autoload.php";

$app = require_once __DIR__."/bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

use App\Imports\ProductsImportNoHeader;
use Maatwebsite\Excel\Facades\Excel;

echo "🚀 Iniciando importação de produtos RPA...\n\n";

$filePath = public_path("RPA/produtos_rpa.xlsx");

if (!file_exists($filePath)) {
    echo "❌ Erro: Ficheiro não encontrado!\n";
    exit(1);
}

echo "📄 Ficheiro: produtos_rpa.xlsx\n";
echo "📏 Tamanho: " . round(filesize($filePath) / 1024, 2) . " KB\n\n";

// RPA User ID and Shop ID
$userId = 13;
$shopId = 9;

echo "👤 Fornecedor: RPA User (ID: {$userId})\n";
echo "🏪 Loja: RPA Fornecedor (ID: {$shopId})\n\n";

$startTime = microtime(true);

try {
    $import = new ProductsImportNoHeader($userId, $shopId);
    Excel::import($import, $filePath);
    
    $endTime = microtime(true);
    $duration = round($endTime - $startTime, 2);
    
    $imported = $import->getImportedCount();
    $skipped = $import->getSkippedCount();
    $errors = $import->getErrors();
    
    echo "✅ Importação concluída!\n\n";
    echo "📊 RESULTADOS:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📦 Produtos importados: {$imported}\n";
    echo "⏭️ Linhas vazias ignoradas: {$skipped}\n";
    echo "⏱️ Tempo de processamento: {$duration} segundos\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    if (!empty($errors)) {
        echo "⚠️ ERROS ENCONTRADOS (" . count($errors) . "):\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        foreach (array_slice($errors, 0, 10) as $error) {
            echo "  • {$error}\n";
        }
        if (count($errors) > 10) {
            echo "  ... e mais " . (count($errors) - 10) . " erros.\n";
        }
        echo "\n";
    }
    
    // Verify products in database
    $totalProducts = \App\Models\Product::where("user_id", $userId)->count();
    echo "🔍 VERIFICAÇÃO:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Total de produtos do RPA na base de dados: {$totalProducts}\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    echo "✅ Importação concluída com sucesso!\n";
    echo "🌐 Ver produtos em: https://app.kulonda.ao/shop/rpa-fornecedor\n\n";
    
    // Show some sample products
    $sampleProducts = \App\Models\Product::where("user_id", $userId)->limit(5)->get(["id", "name", "unit_price", "current_stock"]);
    if ($sampleProducts->count() > 0) {
        echo "📦 Exemplos de produtos importados:\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        foreach ($sampleProducts as $product) {
            echo "  • [{$product->id}] {$product->name} - {$product->unit_price} AOA (Stock: {$product->current_stock})\n";
        }
    }
    
} catch (\Exception $e) {
    echo "❌ Erro ao importar: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
