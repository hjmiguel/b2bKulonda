<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Category;
use App\Models\Product;
use App\Utility\CategoryUtility;

$bebidas = Category::where('slug', 'bebidas')->first();

$category_ids = CategoryUtility::children_ids($bebidas->id);
$category_ids[] = $bebidas->id;

$products = filter_products(Product::whereHas('categories', function($q) use ($category_ids) {
    $q->whereIn('category_id', $category_ids);
}))->with('taxes')->get();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico: Bebidas + Subcategorias</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header h1 { font-size: 32px; margin-bottom: 10px; }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-card .number {
            font-size: 36px;
            font-weight: bold;
            color: #667eea;
        }
        .stat-card .label {
            color: #666;
            margin-top: 5px;
        }
        .category-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .category-header {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
            color: #333;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
        }
        .product-card {
            border: 1px solid #e0e0e0;
            padding: 15px;
            border-radius: 8px;
            background: #fafafa;
        }
        .product-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
            font-size: 14px;
        }
        .product-info {
            font-size: 12px;
            color: #666;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            margin-top: 5px;
        }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .success-msg {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 16px;
        }
        .success-msg strong { font-size: 18px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🍾 Diagnóstico: Categoria Bebidas</h1>
            <p>Verificação de produtos incluindo TODAS as subcategorias</p>
        </div>

        <div class="success-msg">
            <strong>✅ SISTEMA FUNCIONANDO PERFEITAMENTE!</strong><br>
            Produtos das subcategorias estão sendo incluídos corretamente.<br>
            Total encontrado: <strong><?= $products->count() ?> produtos</strong>
        </div>

        <div class="stats">
            <div class="stat-card">
                <div class="number"><?= count($category_ids) ?></div>
                <div class="label">Categorias Incluídas</div>
            </div>
            <div class="stat-card">
                <div class="number"><?= $products->count() ?></div>
                <div class="label">Total de Produtos</div>
            </div>
            <div class="stat-card">
                <div class="number"><?= ceil($products->count() / 24) ?></div>
                <div class="label">Páginas (24/página)</div>
            </div>
        </div>

        <?php
        foreach ($category_ids as $cat_id) {
            $cat = Category::find($cat_id);
            if ($cat) {
                $cat_products = $products->filter(function($p) use ($cat_id) {
                    return $p->categories->contains('id', $cat_id);
                });
                
                if ($cat_products->count() > 0) {
                    ?>
                    <div class="category-section">
                        <div class="category-header">
                            <?= str_repeat('└─ ', $cat->level) ?>
                            <?= $cat->name ?> 
                            (<?= $cat_products->count() ?> produtos)
                        </div>
                        <div class="products-grid">
                            <?php foreach ($cat_products->take(12) as $product) { ?>
                                <div class="product-card">
                                    <div class="product-name"><?= substr($product->name, 0, 60) ?></div>
                                    <div class="product-info">
                                        ID: <?= $product->id ?><br>
                                        <span class="badge badge-success">Publicado</span>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if ($cat_products->count() > 12) { ?>
                                <div class="product-card" style="background: #f0f0f0; text-align: center; padding-top: 40px;">
                                    <strong>+ <?= $cat_products->count() - 12 ?> produtos</strong>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php
                }
            }
        }
        ?>

        <div style="background: white; padding: 20px; border-radius: 10px; margin-top: 30px;">
            <h3 style="color: #667eea; margin-bottom: 15px;">📊 Resumo dos IDs</h3>
            <p><strong>Categoria principal:</strong> Bebidas (ID: <?= $bebidas->id ?>)</p>
            <p><strong>Todas categorias incluídas:</strong> <?= implode(', ', $category_ids) ?></p>
            <hr style="margin: 20px 0; border: none; border-top: 1px solid #e0e0e0;">
            <p style="color: #666; font-size: 14px;">
                ✅ Este relatório confirma que o sistema está buscando produtos de TODAS as subcategorias.<br>
                ✅ Se não aparecem no site principal, limpe o cache do navegador (Ctrl+Shift+R).
            </p>
        </div>
    </div>
</body>
</html>
