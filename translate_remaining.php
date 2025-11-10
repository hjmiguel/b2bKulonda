<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Translation;

// Comprehensive translation dictionary for PT-PT
$translations = [
    // Already in Portuguese - skip
    'Ainda não tem conta?' => 'Ainda não tem conta?',
    'Registrar Cliente' => 'Registrar Cliente',
    'Registrar Vendedor' => 'Registrar Vendedor',
    
    // Numbers and sizes - keep as is
    '1' => '1',
    '120x80' => '120x80',
    '200x200' => '200x200',
    '32x32' => '32x32',
    
    // A
    'activated' => 'ativado',
    'activation' => 'ativação',
    'active' => 'ativo',
    'add' => 'adicionar',
    'address' => 'endereço',
    'affiliate' => 'afiliado',
    'all' => 'todos',
    'amount' => 'montante',
    'appearance' => 'aparência',
    'approval' => 'aprovação',
    'approved' => 'aprovado',
    'area' => 'área',
    'attribute' => 'atributo',
    'attributes' => 'atributos',
    'available' => 'disponível',
    'avatar' => 'avatar',
    
    // AWS - keep technical terms
    'aws_access_key_id' => 'aws_access_key_id',
    'aws_bucket' => 'aws_bucket',
    'aws_default_region' => 'aws_default_region',
    'aws_secret_access_key' => 'aws_secret_access_key',
    'aws_url' => 'aws_url',
    
    // B
    'back' => 'voltar',
    'banner' => 'banner',
    'banners' => 'banners',
    'barcode' => 'código de barras',
    'bkash' => 'bkash',
    'blog' => 'blogue',
    'blogs' => 'blogues',
    'bottom_header_bg_color' => 'cor_de_fundo_cabeçalho_inferior',
    'bottom_header_text_color' => 'cor_texto_cabeçalho_inferior',
    'brand' => 'marca',
    'brands' => 'marcas',
    'browse' => 'procurar',
    'business' => 'negócio',
    'buy' => 'comprar',
    
    // C
    'cancel' => 'cancelar',
    'carriers' => 'transportadoras',
    'categories' => 'categorias',
    'category' => 'categoria',
    'center' => 'centro',
    'cities' => 'cidades',
    'city' => 'cidade',
    'classifieds' => 'classificados',
    'clear' => 'limpar',
    'close' => 'fechar',
    'code' => 'código',
    'collection' => 'coleção',
    'color' => 'cor',
    'colors' => 'cores',
    'comment' => 'comentário',
    'commission' => 'comissão',
    'comparison' => 'comparação',
    'complete' => 'completo',
    'condition' => 'condição',
    'configuration' => 'configuração',
    'confirm' => 'confirmar',
    'confirmation' => 'confirmação',
    'confirmed' => 'confirmado',
    'congratulations' => 'parabéns',
    'contact' => 'contacto',
    'contacts' => 'contactos',
    'converted' => 'convertido',
    'copied' => 'copiado',
    'copy' => 'copiar',
    'cost' => 'custo',
    'countries' => 'países',
    'country' => 'país',
    'coupon' => 'cupão',
    'csv' => 'csv',
    'currency' => 'moeda',
    'current' => 'atual',
    'customer' => 'cliente',
    'customers' => 'clientes',
    
    // D
    'dailymotion' => 'dailymotion',
    'dark' => 'escuro',
    'dashboard' => 'painel de controlo',
    'date' => 'data',
    'daterange' => 'intervalo de datas',
    'days' => 'dias',
    'deactivated' => 'desativado',
    'default' => 'predefinido',
    'delete' => 'eliminar',
    'delivered' => 'entregue',
    'description' => 'descrição',
    'digital' => 'digital',
    'discount' => 'desconto',
    'documents' => 'documentos',
    'done' => 'concluído',
    'download' => 'transferir',
    'due' => 'pendente',
    'duplicate' => 'duplicar',
    'duration' => 'duração',
];

echo "Starting batch translation...\n";
echo "Total translations in dictionary: " . count($translations) . "\n\n";

$inserted = 0;
$skipped = 0;
$batch = [];

foreach ($translations as $en => $pt) {
    // Check if already exists
    $existing = Translation::where('lang', 'pt')->where('lang_key', $en)->first();
    
    if ($existing) {
        $skipped++;
        continue;
    }
    
    // Add to batch
    $batch[] = [
        'lang' => 'pt',
        'lang_key' => $en,
        'lang_value' => $pt,
        'created_at' => now(),
        'updated_at' => now(),
    ];
    
    // Insert in batches of 100
    if (count($batch) >= 100) {
        Translation::insert($batch);
        $inserted += count($batch);
        echo "Inserted " . count($batch) . " translations (Total: $inserted)\n";
        $batch = [];
    }
}

// Insert remaining
if (count($batch) > 0) {
    Translation::insert($batch);
    $inserted += count($batch);
    echo "Inserted " . count($batch) . " translations (Total: $inserted)\n";
}

echo "\n=== Summary ===\n";
echo "Inserted: $inserted\n";
echo "Skipped (already exist): $skipped\n";
echo "Total EN: " . Translation::where('lang', 'en')->count() . "\n";
echo "Total PT: " . Translation::where('lang', 'pt')->count() . "\n";
