<?php
/**
 * Análise e Configuração do Sistema Kulonda para Angola
 * 
 * Este script analisa o sistema atual e fornece recomendações
 * para adaptar completamente à realidade angolana
 */

echo "==========================================================\n";
echo "   ANÁLISE DO SISTEMA KULONDA - REALIDADE ANGOLA\n";
echo "==========================================================\n\n";

// Conectar ao banco de dados
require __DIR__./vendor/autoload.php;
$app = require_once __DIR__./bootstrap/app.php;
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "📊 1. ANÁLISE DE CONFIGURAÇÕES ATUAIS\n";
echo str_repeat("-", 60) . "\n\n";

// Verificar timezone
$timezone = config('app.timezone');
echo "⏰ Timezone Atual: " . $timezone . "\n";
if ($timezone !== 'Africa/Luanda') {
    echo "   ⚠️  RECOMENDAÇÃO: Alterar para 'Africa/Luanda'\n";
}

// Verificar idioma
$locale = config('app.locale');
echo "🌍 Idioma Padrão: " . $locale . "\n";
if ($locale !== 'pt') {
    echo "   ⚠️  RECOMENDAÇÃO: Alterar para 'pt' (Português)\n";
}

echo "\n📊 2. ANÁLISE DE MOEDA\n";
echo str_repeat("-", 60) . "\n\n";

// Verificar moedas cadastradas
if (Schema::hasTable('currencies')) {
    $currencies = DB::table('currencies')->get();
    echo "💰 Moedas Cadastradas:\n";
    foreach ($currencies as $currency) {
        $status = $currency->status == 1 ? "✅ Ativo" : "❌ Inativo";
        echo "   - {$currency->name} ({$currency->code}) - {$currency->symbol} - {$status}\n";
    }
    
    $aoa = DB::table('currencies')->where('code', 'AOA')->first();
    if (!$aoa) {
        echo "\n   ⚠️  KWANZA (AOA) NÃO CADASTRADO!\n";
        echo "   💡 AÇÃO NECESSÁRIA: Adicionar Kwanza Angolano (AOA)\n";
    } else {
        if ($aoa->status != 1) {
            echo "\n   ⚠️  Kwanza existe mas está INATIVO\n";
        } else {
            echo "\n   ✅ Kwanza Angolano (AOA) está cadastrado e ativo\n";
        }
    }
}

echo "\n📊 3. ANÁLISE DE IMPOSTOS\n";
echo str_repeat("-", 60) . "\n\n";

if (Schema::hasTable('taxes')) {
    $taxes = DB::table('taxes')->get();
    echo "💵 Impostos Cadastrados:\n";
    foreach ($taxes as $tax) {
        $status = $tax->tax_status == 1 ? "✅ Ativo" : "❌ Inativo";
        echo "   - {$tax->name} - {$status}\n";
    }
    
    $iva = DB::table('taxes')->where('name', 'LIKE', '%IVA%')->first();
    if (!$iva) {
        echo "\n   ⚠️  IVA (Imposto sobre Valor Acrescentado) NÃO ENCONTRADO!\n";
        echo "   💡 Em Angola: IVA padrão = 14%\n";
        echo "   💡 AÇÃO NECESSÁRIA: Criar imposto IVA 14%\n";
    }
}

echo "\n📊 4. ANÁLISE DE MÉTODOS DE PAGAMENTO\n";
echo str_repeat("-", 60) . "\n\n";

// Verificar ProxyPay
$proxypayEnv = env('PROXYPAY_ENVIRONMENT');
$proxypayEntity = env('PROXYPAY_ENTITY');
$proxypayApiKey = env('PROXYPAY_PRODUCTION_API_KEY');

echo "💳 ProxyPay (Pagamento Mobile Angola):\n";
echo "   Ambiente: " . ($proxypayEnv ?: 'Não configurado') . "\n";
echo "   Entidade: " . ($proxypayEntity ?: 'Não configurado') . "\n";
echo "   API Key: " . ($proxypayApiKey ? "✅ Configurada" : "❌ Não configurada") . "\n";

if ($proxypayEnv && $proxypayEntity && $proxypayApiKey) {
    echo "   ✅ ProxyPay está CONFIGURADO e ATIVO\n";
} else {
    echo "   ⚠️  ProxyPay parcialmente configurado\n";
}

echo "\n📊 5. ANÁLISE DE CONFIGURAÇÕES DE NEGÓCIO\n";
echo str_repeat("-", 60) . "\n\n";

if (Schema::hasTable('business_settings')) {
    $businessSettings = DB::table('business_settings')
        ->whereIn('type', [
            'system_default_currency',
            'currency_format',
            'decimal_separator',
            'symbol_format'
        ])
        ->get();
    
    echo "⚙️  Configurações de Negócio:\n";
    foreach ($businessSettings as $setting) {
        echo "   {$setting->type}: " . $setting->value . "\n";
    }
}

echo "\n📊 6. VERIFICAÇÃO DE FATURAÇÃO ELETRÓNICA (AGT)\n";
echo str_repeat("-", 60) . "\n\n";

$agtEnabled = env('AGT_ENABLED', false);
$agtNif = env('AGT_NIF');

echo "🏛️  Integração AGT:\n";
echo "   Ativado: " . ($agtEnabled ? "✅ Sim" : "❌ Não") . "\n";
echo "   NIF Empresa: " . ($agtNif ?: "⚠️  Não configurado") . "\n";

if (file_exists(__DIR__./config/agt.php)) {
    echo "   ✅ Arquivo de configuração AGT existe\n";
} else {
    echo "   ❌ Arquivo de configuração AGT não encontrado\n";
}

if (file_exists(__DIR__./storage/certificates/agt/public_key.pem)) {
    echo "   ✅ Certificado digital criado\n";
} else {
    echo "   ❌ Certificado digital não encontrado\n";
}

echo "\n📊 7. RECOMENDAÇÕES PARA ANGOLA\n";
echo str_repeat("=", 60) . "\n\n";

$recommendations = [
    'URGENTE' => [
        'Configurar timezone para Africa/Luanda',
        'Adicionar/ativar moeda AOA (Kwanza)',
        'Criar imposto IVA 14%',
        'Configurar NIF da empresa no .env',
    ],
    'IMPORTANTE' => [
        'Testar integração ProxyPay em produção',
        'Submeter CSR para certificação AGT',
        'Configurar formato de moeda angolano (Kz)',
        'Validar traduções em português de Angola',
    ],
    'RECOMENDADO' => [
        'Adicionar Multicaixa Express como método de pagamento',
        'Configurar taxas de entrega para Angola',
        'Adicionar regiões/províncias de Angola',
        'Configurar numeração de faturas conforme AGT',
    ]
];

foreach ($recommendations as $priority => $items) {
    $icon = $priority === 'URGENTE' ? '🔴' : ($priority === 'IMPORTANTE' ? '🟠' : '🟡');
    echo "{$icon} {$priority}:\n";
    foreach ($items as $item) {
        echo "   • {$item}\n";
    }
    echo "\n";
}

echo "\n📊 8. CONFIGURAÇÕES ESPECÍFICAS PARA ANGOLA\n";
echo str_repeat("=", 60) . "\n\n";

echo "🇦🇴 Parâmetros Recomendados:\n\n";
echo "Moeda:\n";
echo "   - Nome: Kwanza Angolano\n";
echo "   - Código: AOA\n";
echo "   - Símbolo: Kz ou AOA\n";
echo "   - Exchange Rate: 1.0 (se AOA for moeda base)\n\n";

echo "Impostos:\n";
echo "   - IVA (Imposto sobre Valor Acrescentado): 14%\n";
echo "   - Regime: Geral, Transitório, ou Exclusão\n";
echo "   - Tipos de documentos: Fatura, Fatura-Recibo, Nota de Crédito\n\n";

echo "Métodos de Pagamento:\n";
echo "   - ProxyPay ✅ (já configurado)\n";
echo "   - Multicaixa Express (integração necessária)\n";
echo "   - Transferência Bancária\n";
echo "   - Numerário (Cash on Delivery)\n\n";

echo "Timezone:\n";
echo "   - Africa/Luanda (WAT - West Africa Time, UTC+1)\n\n";

echo "Faturação:\n";
echo "   - Série padrão: A, B, C, etc.\n";
echo "   - Formato: FT A/2025/00001\n";
echo "   - QR Code obrigatório (AGT)\n";
echo "   - Hash de documento (assinatura digital)\n\n";

echo "==========================================================\n";
echo "   FIM DA ANÁLISE\n";
echo "==========================================================\n\n";

echo "💡 Próximos passos:\n";
echo "   1. Revisar recomendações acima\n";
echo "   2. Executar script de configuração automática\n";
echo "   3. Testar todas as funcionalidades\n";
echo "   4. Validar com ambiente AGT de homologação\n\n";
