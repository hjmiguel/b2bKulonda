<?php

require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

use App\Models\Product;

echo "🎨 Criando descrições criativas para produtos RPA...\n\n";

function generateCreativeDescription($productName, $price, $stock) {
    $name = strtoupper($productName);
    
    // Identify product type
    $isWater = stripos($name, "AGUA") !== false || stripos($name, "ÁGUA") !== false;
    $isBeer = stripos($name, "CERVEJA") !== false || stripos($name, "BEER") !== false;
    $isSoda = stripos($name, "COCA") !== false || stripos($name, "FANTA") !== false || 
              stripos($name, "SPRITE") !== false || stripos($name, "SUMOL") !== false;
    $isJuice = stripos($name, "SUCO") !== false || stripos($name, "SUMO") !== false;
    $isEnergy = stripos($name, "SPEED") !== false || stripos($name, "ENERGY") !== false;
    $isTonic = stripos($name, "TONICA") !== false || stripos($name, "GINGER") !== false;
    
    $descriptions = [];
    
    if ($isWater) {
        $descriptions[] = "Descubra a pureza e frescura de {$productName}, uma água mineral de qualidade superior selecionada especialmente para você. Proveniente de fontes naturais cuidadosamente protegidas, este produto representa o equilíbrio perfeito entre saúde e sabor refrescante.

Ideal para manter-se hidratado durante todo o dia, seja no trabalho, em casa ou em atividades físicas. Cada embalagem é garantia de qualidade, pureza e confiança. Disponível agora com entrega rápida e segura.";
    }
    elseif ($isBeer) {
        $descriptions[] = "{$productName} é a escolha perfeita para quem aprecia uma cerveja de qualidade excepcional. Com seu sabor característico e refrescante, este produto é ideal para momentos de descontração, celebrações ou simplesmente para apreciar o melhor da vida.

Fabricada com ingredientes selecionados e seguindo rigorosos padrões de qualidade, esta cerveja oferece uma experiência única a cada gole. Perfeita para servir gelada em encontros com amigos, churrascos, festas ou aquele merecido momento de relaxamento após um dia produtivo. Adquira já e desfrute de uma experiência premium.";
    }
    elseif ($isSoda) {
        $descriptions[] = "Experimente o sabor inconfundível e refrescante de {$productName}, o refrigerante que conquista paladares há gerações. Com sua fórmula única e qualidade reconhecida mundialmente, este produto é sinônimo de momentos especiais e celebrações inesquecíveis.

Ideal para acompanhar refeições, festas, eventos ou simplesmente para refrescar o seu dia. Cada lata ou garrafa é cuidadosamente preparada para garantir o máximo de sabor e frescor. Perfeito para toda a família, este refrigerante transforma qualquer ocasião em um momento memorável.";
    }
    elseif ($isJuice) {
        $descriptions[] = "Delicie-se com {$productName}, uma bebida natural e saborosa que traz toda a essência e vitaminas das melhores frutas. Preparado com ingredientes cuidadosamente selecionados, este suco oferece não apenas sabor excepcional, mas também nutrição e bem-estar para você e sua família.

Rico em vitaminas e nutrientes essenciais, é a escolha perfeita para começar o dia com energia, acompanhar refeições saudáveis ou simplesmente refrescar-se com qualidade. Sem conservantes artificiais e com todo o sabor natural que você merece. Cuide da sua saúde com prazer!";
    }
    elseif ($isEnergy) {
        $descriptions[] = "{$productName} é a bebida energética que você precisa para enfrentar desafios e manter-se ativo durante todo o dia. Formulada especialmente para proporcionar energia rápida e duradoura, este produto combina ingredientes funcionais com sabor irresistível.

Perfeito para estudantes, profissionais, atletas e todos que precisam de um impulso extra de energia e concentração. Seja para trabalhar, estudar, praticar esportes ou simplesmente manter-se alerta, esta bebida energética é seu aliado ideal. Adquira já e sinta a diferença!";
    }
    elseif ($isTonic) {
        $descriptions[] = "Descubra o sabor sofisticado e refrescante de {$productName}, uma bebida premium que eleva qualquer momento a um patamar superior. Com seu perfil único de sabor e qualidade incomparável, este produto é escolha certa para quem aprecia o requinte e a excelência.

Ideal para criar drinks especiais, acompanhar momentos de relaxamento ou simplesmente apreciar puro bem gelado. Seu sabor distintivo e equilibrado torna cada gole uma experiência sensorial única. Perfeito para impressionar convidados ou presentear quem você ama.";
    }
    else {
        $descriptions[] = "Apresentamos {$productName}, um produto premium cuidadosamente selecionado para atender aos mais exigentes padrões de qualidade. Com características únicas e sabor excepcional, este item representa o melhor que o mercado pode oferecer.

Perfeito para diversas ocasiões e momentos especiais, este produto combina qualidade, sabor e praticidade. Seja para consumo pessoal, eventos, festas ou presentear alguém especial, você estará fazendo a escolha certa. Disponível com estoque garantido e entrega rápida.";
    }
    
    return $descriptions[0];
}

$products = Product::where("user_id", 13)->get();
$updated = 0;

foreach ($products as $product) {
    $newDescription = generateCreativeDescription(
        $product->name,
        $product->unit_price,
        $product->current_stock
    );
    
    $product->description = $newDescription;
    $product->meta_description = substr($newDescription, 0, 160);
    $product->save();
    
    $updated++;
    
    if ($updated % 50 == 0) {
        echo "Processados: {$updated} produtos...\n";
    }
}

echo "\n✅ Descrições criadas com sucesso!\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📝 Total de produtos atualizados: {$updated}\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Show examples
echo "📦 Exemplos de descrições criadas:\n\n";
$samples = Product::where("user_id", 13)->limit(5)->get(["name", "description"]);
foreach ($samples as $sample) {
    echo "Produto: {$sample->name}\n";
    echo "Descrição:\n{$sample->description}\n";
    echo "\n" . str_repeat("─", 80) . "\n\n";
}
