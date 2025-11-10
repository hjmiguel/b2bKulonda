<?php
/**
 * Script de Importação em Massa - Produtos CUCA
 * Sistema: Kulonda B2B HORECA
 * VERSÃO CORRIGIDA - Usa apenas campos existentes
 */

require __DIR__ . '/../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "\n";
echo "╔════════════════════════════════════════════════════════╗\n";
echo "║   IMPORTAÇÃO EM MASSA - PRODUTOS CUCA                 ║\n";
echo "║   Sistema Kulonda B2B HORECA                          ║\n";
echo "╚════════════════════════════════════════════════════════╝\n";
echo "\n";

// Ler JSON com produtos
$jsonFile = __DIR__ . '/cuca_products.json';

if (!file_exists($jsonFile)) {
    die("❌ Erro: Arquivo $jsonFile não encontrado!\n");
}

$jsonContent = file_get_contents($jsonFile);
$data = json_decode($jsonContent, true);

if (!$data) {
    die("❌ Erro ao decodificar JSON!\n");
}

$products = $data['products'];
$metadata = $data['metadata'];

echo "📦 Total de produtos a importar: " . count($products) . "\n";
echo "🏭 Marca ID: " . $metadata['brand_id'] . " (CUCA)\n";
echo "👤 Vendedor ID: " . $metadata['user_id'] . " (CUCA)\n";
echo "🏪 Loja ID: " . $metadata['shop_id'] . " (CUCA)\n";
echo "\n";

$now = Carbon::now();
$imported = 0;
$errors = 0;

echo "🚀 Iniciando importação...\n\n";

foreach ($products as $index => $product) {
    try {
        $productNumber = $index + 1;

        // Preparar dados do produto - APENAS CAMPOS EXISTENTES
        $productData = [
            'name' => $product['name'],
            'slug' => $product['slug'],
            'added_by' => 'seller',
            'user_id' => $product['user_id'],
            'category_id' => $product['category_id'],
            'brand_id' => $product['brand_id'],
            'unit_id' => $product['unit_id'],
            'unit' => $product['volume'] . $product['volume_unit'],
            'description' => $product['description'],
            'unit_price' => $product['unit_price'],
            'purchase_price' => $product['purchase_price'],
            'current_stock' => $product['current_stock'],
            'min_qty' => 1,
            'low_stock_quantity' => 10,
            'discount' => $product['discount'],
            'discount_type' => $product['discount_type'],
            'tax' => $product['tax'],
            'tax_type' => $product['tax_type'],
            'shipping_type' => $product['shipping_type'],
            'shipping_cost' => 0,
            'est_shipping_days' => $product['est_shipping_days'],
            'published' => $product['published'],
            'approved' => 1,
            'featured' => $product['featured'],
            'seller_featured' => 0,
            'todays_deal' => $product['todays_deal'],
            'cash_on_delivery' => 1,
            'stock_visibility_state' => 'quantity',
            'meta_title' => $product['meta_title'],
            'meta_description' => $product['meta_description'],
            'num_of_sale' => 0,
            'rating' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        // Inserir produto
        $productId = DB::table('products')->insertGetId($productData);

        // Criar traduções PT
        DB::table('product_translations')->insert([
            'product_id' => $productId,
            'name' => $product['name'],
            'unit' => $product['volume'] . $product['volume_unit'],
            'description' => $product['description'],
            'lang' => 'pt',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Criar traduções EN
        DB::table('product_translations')->insert([
            'product_id' => $productId,
            'name' => $product['name'],
            'unit' => $product['volume'] . $product['volume_unit'],
            'description' => $product['description'],
            'lang' => 'en',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Criar stock record
        DB::table('product_stocks')->insert([
            'product_id' => $productId,
            'variant' => '',
            'sku' => 'CUCA-' . str_pad($productId, 6, '0', STR_PAD_LEFT),
            'qty' => $product['current_stock'],
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $imported++;

        $categoryName = '';
        if ($product['category_id'] == 72) $categoryName = 'Cervejas';
        elseif ($product['category_id'] == 75) $categoryName = 'Refrigerantes';
        elseif ($product['category_id'] == 76) $categoryName = 'Sumos';

        echo sprintf(
            "  [%02d/%02d] ✓ %-50s | %-15s | %10.2f Kz\n",
            $productNumber,
            count($products),
            substr($product['name'], 0, 50),
            $categoryName,
            $product['unit_price']
        );

    } catch (Exception $e) {
        $errors++;
        echo sprintf(
            "  [%02d/%02d] ❌ ERRO: %s - %s\n",
            $productNumber,
            count($products),
            $product['name'],
            $e->getMessage()
        );
    }
}

echo "\n";
echo "╔════════════════════════════════════════════════════════╗\n";
echo "║   IMPORTAÇÃO CONCLUÍDA                                ║\n";
echo "╚════════════════════════════════════════════════════════╝\n";
echo "\n";
echo "✓ Produtos importados: $imported\n";
echo "✗ Erros: $errors\n";
echo "📊 Taxa de sucesso: " . round(($imported / count($products)) * 100, 2) . "%\n";
echo "\n";
echo "🔗 URLs:\n";
echo "   Admin: https://app.kulonda.ao/admin/products\n";
echo "   Seller: https://app.kulonda.ao/seller/products\n";
echo "\n";
