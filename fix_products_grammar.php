<?php

require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

use App\Models\Product;

echo "🔧 Corrigindo gramática e codificação dos produtos RPA...\n\n";

// Fix encoding function
function fixEncoding($text) {
    // Common UTF-8 encoding issues
    $replacements = [
        "Ã¡" => "á",
        "Ã©" => "é",
        "Ã­" => "í",
        "Ã³" => "ó",
        "Ãº" => "ú",
        "Ã£" => "ã",
        "Ã§" => "ç",
        "Ãª" => "ê",
        "Ã´" => "ô",
        "Ã " => "à",
        "Ã¨" => "è",
        "Ã¬" => "ì",
        "Ã²" => "ò",
        "Ã¹" => "ù",
        "Ã‡" => "Ç",
        "Ã" => "Á",
        "Ã‰" => "É",
        "Ã" => "Í",
        "Ã"" => "Ó",
        "Ãš" => "Ú",
        "Ãƒ" => "Ã",
        "Ã_x0081_" => "Á",
        "monetÃ¡rias" => "AOA",
        "unidades monetÃ¡rias" => "AOA",
    ];
    
    $text = str_replace(array_keys($replacements), array_values($replacements), $text);
    
    // Remove remaining broken characters
    $text = preg_replace("/Ã[^ ]*/", "", $text);
    
    return trim($text);
}

// Get all RPA products
$products = Product::where("user_id", 13)->get();

$fixedCount = 0;
$deletedCount = 0;

foreach ($products as $product) {
    $updated = false;
    
    // Delete invalid product (post_title)
    if ($product->name === "post_title" || empty(trim($product->name))) {
        $product->delete();
        $deletedCount++;
        echo "❌ Deletado produto inválido: ID {$product->id}\n";
        continue;
    }
    
    // Fix name
    $originalName = $product->name;
    $fixedName = fixEncoding($product->name);
    
    // Fix article: "O AGUA" -> "ÁGUA" (remove article)
    $fixedName = preg_replace("/^O (AGUA|Agua)/", "ÁGUA", $fixedName);
    $fixedName = preg_replace("/^A (AGUA|Agua)/", "ÁGUA", $fixedName);
    
    if ($fixedName !== $originalName) {
        $product->name = $fixedName;
        $product->meta_title = $fixedName;
        $updated = true;
    }
    
    // Fix description
    if (!empty($product->description)) {
        $originalDesc = $product->description;
        $fixedDesc = fixEncoding($product->description);
        
        // Improve generic descriptions
        if (strpos($fixedDesc, "disponível na categoria") !== false) {
            // Create better description
            $fixedDesc = "Produto de qualidade premium. " . $product->name . ". Disponível para entrega imediata.";
        }
        
        if ($fixedDesc !== $originalDesc) {
            $product->description = $fixedDesc;
            $product->meta_description = substr($fixedDesc, 0, 160);
            $updated = true;
        }
    }
    
    if ($updated) {
        $product->save();
        $fixedCount++;
    }
}

echo "\n✅ Correção concluída!\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📝 Produtos corrigidos: {$fixedCount}\n";
echo "❌ Produtos deletados: {$deletedCount}\n";
echo "✅ Produtos finais: " . Product::where("user_id", 13)->count() . "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Show some examples
echo "📦 Exemplos de produtos corrigidos:\n";
$samples = Product::where("user_id", 13)->limit(10)->get(["id", "name", "description"]);
foreach ($samples as $p) {
    echo "  • [{$p->id}] {$p->name}\n";
    echo "    Descrição: " . substr($p->description, 0, 60) . "...\n";
}
