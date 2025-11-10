<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$products = DB::table('products')
    ->leftJoin('product_translations', function($join) {
        $join->on('products.id', '=', 'product_translations.product_id')
             ->where('product_translations.lang', '=', 'pt');
    })
    ->where('products.user_id', 12)
    ->whereNull('products.thumbnail_img')
    ->select('products.id', 'product_translations.name')
    ->limit(50)
    ->get();

echo "Products without images: " . $products->count() . "\n\n";
foreach($products as $p) {
    echo $p->id . " - " . $p->name . "\n";
}
