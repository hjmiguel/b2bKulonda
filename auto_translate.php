<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Translation;

// Read untranslated strings
$json = file_get_contents(__DIR__ . '/all_untranslated_strings.json');
$strings = json_decode($json, true);

echo "Total strings to process: " . count($strings) . "\n\n";

// Comprehensive translation mapping
$autoTranslate = function($en) {
    // Already Portuguese - return as is
    if (preg_match('/[ãõçáéíóúâêôàèìòù]/u', $en)) {
        return $en;
    }
    
    // Technical terms - keep as is
    $technical = ['aws_', 'flw_', 'mim_', 'mpesa_', 'payfast_', 'iyzico_', 'rave_', 'proxypay_', 'BACKBLAZE_', 'AUTH'];
    foreach ($technical as $prefix) {
        if (str_contains($en, $prefix)) {
            return $en;
        }
    }
    
    // Numbers and dimensions - keep as is
    if (preg_match('/^\d/', $en) || preg_match('/\d+x\d+/', $en) || preg_match('/\d+px/', $en)) {
        return $en;
    }
    
    // Brand names and payment systems
    $brands = ['bkash', 'flutterwave', 'instamojo', 'iyzico', 'nagad', 'ngenius', 'payfast', 'payhere', 
                'paypal', 'paystack', 'paytm', 'proxypay', 'razorpay', 'sslcommerz', 'stripe', 'voguepay',
                'dailymotion', 'youtube', 'vimeo', 'instagram', 'facebook', 'google', 'twitter', 'mpesa',
                'mailgun', 'sendmail', 'smtp', 'csv', 'pdf', 'jpeg', 'png', 'webp', 'rtl', 'seo', 'pos',
                'sku', 'url', 'api', 'rgb', 'html'];
    if (in_array(strtolower($en), $brands)) {
        return $en;
    }
    
    // Dictionary of translations
    $dict = include(__DIR__ . '/translations_dictionary.php');
    
    return $dict[strtolower($en)] ?? $en;
};

// Create translations dictionary file
$dictContent = "<?php\nreturn [\n";
$dictContent .= "    // Single words\n";
$dictContent .= "    'activated' => 'ativado',\n";
$dictContent .= "    'activation' => 'ativação',\n";
$dictContent .= "    'active' => 'ativo',\n";
$dictContent .= "    'add' => 'adicionar',\n";
$dictContent .= "    'address' => 'endereço',\n";
$dictContent .= "    'affiliate' => 'afiliado',\n";
$dictContent .= "    'all' => 'todos',\n";
$dictContent .= "    'amount' => 'montante',\n";
$dictContent .= "    'appearance' => 'aparência',\n";
$dictContent .= "    'approval' => 'aprovação',\n";
$dictContent .= "    'approved' => 'aprovado',\n";
$dictContent .= "    'area' => 'área',\n";
$dictContent .= "    'attribute' => 'atributo',\n";
$dictContent .= "    'attributes' => 'atributos',\n";
$dictContent .= "    'available' => 'disponível',\n";
$dictContent .= "    'avatar' => 'avatar',\n";
$dictContent .= "    'back' => 'voltar',\n";
$dictContent .= "    'banner' => 'banner',\n";
$dictContent .= "    'banners' => 'banners',\n";
$dictContent .= "    'barcode' => 'código de barras',\n";
$dictContent .= "    'blog' => 'blogue',\n";
$dictContent .= "    'blogs' => 'blogues',\n";
$dictContent .= "    'brand' => 'marca',\n";
$dictContent .= "    'brands' => 'marcas',\n";
$dictContent .= "    'browse' => 'procurar',\n";
$dictContent .= "    'business' => 'negócio',\n";
$dictContent .= "    'buy' => 'comprar',\n";
$dictContent .= "    'cancel' => 'cancelar',\n";
$dictContent .= "    'carriers' => 'transportadoras',\n";
$dictContent .= "    'categories' => 'categorias',\n";
$dictContent .= "    'category' => 'categoria',\n";
$dictContent .= "    'center' => 'centro',\n";
$dictContent .= "    'cities' => 'cidades',\n";
$dictContent .= "    'city' => 'cidade',\n";
$dictContent .= "    'classifieds' => 'classificados',\n";
$dictContent .= "    'clear' => 'limpar',\n";
$dictContent .= "    'close' => 'fechar',\n";
$dictContent .= "    'code' => 'código',\n";
$dictContent .= "    'collection' => 'coleção',\n";
$dictContent .= "    'color' => 'cor',\n";
$dictContent .= "    'colors' => 'cores',\n";
$dictContent .= "    'comment' => 'comentário',\n";
$dictContent .= "    'commission' => 'comissão',\n";
$dictContent .= "    'comparison' => 'comparação',\n";
$dictContent .= "    'complete' => 'completo',\n";
$dictContent .= "    'condition' => 'condição',\n";
$dictContent .= "    'configuration' => 'configuração',\n";
$dictContent .= "    'confirm' => 'confirmar',\n";
$dictContent .= "    'confirmation' => 'confirmação',\n";
$dictContent .= "    'confirmed' => 'confirmado',\n";
$dictContent .= "    'congratulations' => 'parabéns',\n";
$dictContent .= "    'contact' => 'contacto',\n";
$dictContent .= "    'contacts' => 'contactos',\n";
$dictContent .= "    'converted' => 'convertido',\n";
$dictContent .= "    'copied' => 'copiado',\n";
$dictContent .= "    'copy' => 'copiar',\n";
$dictContent .= "    'cost' => 'custo',\n";
$dictContent .= "    'countries' => 'países',\n";
$dictContent .= "    'country' => 'país',\n";
$dictContent .= "    'coupon' => 'cupão',\n";
$dictContent .= "    'currency' => 'moeda',\n";
$dictContent .= "    'current' => 'atual',\n";
$dictContent .= "    'customer' => 'cliente',\n";
$dictContent .= "    'customers' => 'clientes',\n";
$dictContent .= "    'dark' => 'escuro',\n";
$dictContent .= "    'dashboard' => 'painel de controlo',\n";
$dictContent .= "    'date' => 'data',\n";
$dictContent .= "    'daterange' => 'intervalo de datas',\n";
$dictContent .= "    'days' => 'dias',\n";
$dictContent .= "    'deactivated' => 'desativado',\n";
$dictContent .= "    'default' => 'predefinido',\n";
$dictContent .= "    'delete' => 'eliminar',\n";
$dictContent .= "    'delivered' => 'entregue',\n";
$dictContent .= "    'description' => 'descrição',\n";
$dictContent .= "    'digital' => 'digital',\n";
$dictContent .= "    'discount' => 'desconto',\n";
$dictContent .= "    'documents' => 'documentos',\n";
$dictContent .= "    'done' => 'concluído',\n";
$dictContent .= "    'download' => 'transferir',\n";
$dictContent .= "    'due' => 'pendente',\n";
$dictContent .= "    'duplicate' => 'duplicar',\n";
$dictContent .= "    'duration' => 'duração',\n";
$dictContent .= "    'earning' => 'ganho',\n";
$dictContent .= "    'earnings' => 'ganhos',\n";
$dictContent .= "    'edit' => 'editar',\n";
$dictContent .= "    'element' => 'elemento',\n";
$dictContent .= "    'email' => 'email',\n";
$dictContent .= "    'ended' => 'terminado',\n";
$dictContent .= "    'export' => 'exportar',\n";
$dictContent .= "    'file' => 'ficheiro',\n";
$dictContent .= "    'files' => 'ficheiros',\n";
$dictContent .= "    'filter' => 'filtrar',\n";
$dictContent .= "    'flat' => 'fixo',\n";
$dictContent .= "    'followers' => 'seguidores',\n";
$dictContent .= "    'footer' => 'rodapé',\n";
$dictContent .= "    'frontend' => 'interface',\n";
$dictContent .= "    'general' => 'geral',\n";
$dictContent .= "    'header' => 'cabeçalho',\n";
$dictContent .= "    'heading' => 'título',\n";
$dictContent .= "    'hello' => 'olá',\n";
$dictContent .= "    'helpline' => 'linha de apoio',\n";
$dictContent .= "    'here' => 'aqui',\n";
$dictContent .= "    'hot' => 'popular',\n";
$dictContent .= "    'image' => 'imagem',\n";
$dictContent .= "    'index' => 'índice',\n";
$dictContent .= "    'instruction' => 'instrução',\n";
$dictContent .= "    'invoice' => 'fatura',\n";
$dictContent .= "    'items' => 'itens',\n";
$dictContent .= "    'kg' => 'kg',\n";
$dictContent .= "    'label' => 'etiqueta',\n";
$dictContent .= "    'language' => 'idioma',\n";
$dictContent .= "    'less' => 'menos',\n";
$dictContent .= "    'level' => 'nível',\n";
$dictContent .= "    'light' => 'claro',\n";
$dictContent .= "    'link' => 'ligação',\n";
$dictContent .= "    'links' => 'ligações',\n";
$dictContent .= "    'location' => 'localização',\n";
$dictContent .= "    'logo' => 'logótipo',\n";
$dictContent .= "    'logout' => 'terminar sessão',\n";
$dictContent .= "    'low' => 'baixo',\n";
$dictContent .= "    'manager' => 'gestor',\n";
$dictContent .= "    'marketing' => 'marketing',\n";
$dictContent .= "    'method' => 'método',\n";
$dictContent .= "    'more' => 'mais',\n";
$dictContent .= "    'name' => 'nome',\n";
$dictContent .= "    'namibia' => 'namíbia',\n";
$dictContent .= "    'newest' => 'mais recente',\n";
$dictContent .= "    'newsletters' => 'newsletters',\n";
$dictContent .= "    'notes' => 'notas',\n";
$dictContent .= "    'notification' => 'notificação',\n";
$dictContent .= "    'notifications' => 'notificações',\n";
$dictContent .= "    'oldest' => 'mais antigo',\n";
$dictContent .= "    'open' => 'abrir',\n";
$dictContent .= "    'option' => 'opção',\n";
$dictContent .= "    'options' => 'opções',\n";
$dictContent .= "    'or' => 'ou',\n";
$dictContent .= "    'order' => 'encomenda',\n";
$dictContent .= "    'owner' => 'proprietário',\n";
$dictContent .= "    'payment' => 'pagamento',\n";
$dictContent .= "    'payouts' => 'pagamentos',\n";
$dictContent .= "    'percent' => 'percentagem',\n";
$dictContent .= "    'point' => 'ponto',\n";
$dictContent .= "    'points' => 'pontos',\n";
$dictContent .= "    'preorders' => 'pré-encomendas',\n";
$dictContent .= "    'prev' => 'anterior',\n";
$dictContent .= "    'processing' => 'a processar',\n";
$dictContent .= "    'purchased' => 'comprado',\n";
$dictContent .= "    'radio' => 'rádio',\n";
$dictContent .= "    'reason' => 'motivo',\n";
$dictContent .= "    'receiver' => 'destinatário',\n";
$dictContent .= "    'reciept' => 'recibo',\n";
$dictContent .= "    'refundable' => 'reembolsável',\n";
$dictContent .= "    'registration' => 'registo',\n";
$dictContent .= "    'regular' => 'regular',\n";
$dictContent .= "    'remove' => 'remover',\n";
$dictContent .= "    'report' => 'relatório',\n";
$dictContent .= "    'route' => 'rota',\n";
$dictContent .= "    'setting' => 'definição',\n";
$dictContent .= "    'share' => 'partilhar',\n";
$dictContent .= "    'slug' => 'slug',\n";
$dictContent .= "    'staff' => 'funcionário',\n";
$dictContent .= "    'states' => 'estados',\n";
$dictContent .= "    'sticker' => 'autocolante',\n";
$dictContent .= "    'stock' => 'stock',\n";
$dictContent .= "    'subcategory' => 'subcategoria',\n";
$dictContent .= "    'subscribe' => 'subscrever',\n";
$dictContent .= "    'subsubcategory' => 'sub-subcategoria',\n";
$dictContent .= "    'subtotal' => 'subtotal',\n";
$dictContent .= "    'tags' => 'etiquetas',\n";
$dictContent .= "    'tax' => 'imposto',\n";
$dictContent .= "    'text' => 'texto',\n";
$dictContent .= "    'ticket' => 'bilhete',\n";
$dictContent .= "    'tickets' => 'bilhetes',\n";
$dictContent .= "    'title' => 'título',\n";
$dictContent .= "    'today' => 'hoje',\n";
$dictContent .= "    'total' => 'total',\n";
$dictContent .= "    'translatable' => 'traduzível',\n";
$dictContent .= "    'translation' => 'tradução',\n";
$dictContent .= "    'type' => 'tipo',\n";
$dictContent .= "    'unit' => 'unidade',\n";
$dictContent .= "    'unpaid' => 'não pago',\n";
$dictContent .= "    'unpublished' => 'não publicado',\n";
$dictContent .= "    'unverified' => 'não verificado',\n";
$dictContent .= "    'upcoming' => 'próximo',\n";
$dictContent .= "    'update' => 'atualizar',\n";
$dictContent .= "    'uploading' => 'a carregar',\n";
$dictContent .= "    'used' => 'usado',\n";
$dictContent .= "    'value' => 'valor',\n";
$dictContent .= "    'values' => 'valores',\n";
$dictContent .= "    'variant' => 'variante',\n";
$dictContent .= "    'variation' => 'variação',\n";
$dictContent .= "    'verified' => 'verificado',\n";
$dictContent .= "    'verify' => 'verificar',\n";
$dictContent .= "    'version' => 'versão',\n";
$dictContent .= "    'video' => 'vídeo',\n";
$dictContent .= "    'videos' => 'vídeos',\n";
$dictContent .= "    'view' => 'ver',\n";
$dictContent .= "    'warranty' => 'garantia',\n";
$dictContent .= "    'week' => 'semana',\n";
$dictContent .= "    'weight' => 'peso',\n";
$dictContent .= "    'white' => 'branco',\n";
$dictContent .= "    'zones' => 'zonas',\n";
$dictContent .= "];\n";

file_put_contents(__DIR__ . '/translations_dictionary.php', $dictContent);
echo "Dictionary file created.\n\n";

// Process first batch
$batch = [];
$inserted = 0;
$skipped = 0;

foreach (array_slice($strings, 0, 300) as $string) {
    $pt = $autoTranslate($string);
    
    // Check if exists
    $existing = Translation::where('lang', 'pt')->where('lang_key', $string)->first();
    
    if ($existing) {
        $skipped++;
        continue;
    }
    
    $batch[] = [
        'lang' => 'pt',
        'lang_key' => $string,
        'lang_value' => $pt,
        'created_at' => now(),
        'updated_at' => now(),
    ];
}

if (count($batch) > 0) {
    Translation::insert($batch);
    $inserted = count($batch);
}

echo "=== Batch 1 Summary ===\n";
echo "Inserted: $inserted\n";
echo "Skipped: $skipped\n";
echo "Total PT translations: " . Translation::where('lang', 'pt')->count() . "\n";
