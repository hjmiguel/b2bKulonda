<?php
// Configuração do banco de dados (do .env)
$dbHost = "localhost";
$dbUser = "u589337713_kulondauser";
$dbPass = "5bN&=$C!@";
$dbName = "u589337713_kulondaDb";

// Conectar ao banco
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Carregar traduções
$translations = json_decode(file_get_contents("translations_complete.json"), true);

echo "═══════════════════════════════════════════════════════════════\n";
echo "  INSERINDO TRADUÇÕES NO BANCO DE DADOS\n";
echo "═══════════════════════════════════════════════════════════════\n\n";
echo "Total de traduções a processar: " . count($translations) . "\n\n";

$inserted = 0;
$updated = 0;
$skipped = 0;
$errors = 0;

// Preparar statements
$checkStmt = $conn->prepare("SELECT id, lang_value FROM translations WHERE lang = ? AND lang_key = ?");
$insertStmt = $conn->prepare("INSERT INTO translations (lang, lang_key, lang_value, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
$updateStmt = $conn->prepare("UPDATE translations SET lang_value = ?, updated_at = NOW() WHERE lang = ? AND lang_key = ?");

$lang = "pt";

foreach ($translations as $key => $value) {
    // Verificar se já existe
    $checkStmt->bind_param("ss", $lang, $key);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows > 0) {
        // Já existe - verificar se precisa atualizar
        $row = $result->fetch_assoc();
        if ($row["lang_value"] !== $value) {
            // Atualizar
            $updateStmt->bind_param("sss", $value, $lang, $key);
            if ($updateStmt->execute()) {
                $updated++;
            } else {
                $errors++;
                echo "❌ Erro ao atualizar: $key\n";
            }
        } else {
            $skipped++;
        }
    } else {
        // Não existe - inserir
        $insertStmt->bind_param("sss", $lang, $key, $value);
        if ($insertStmt->execute()) {
            $inserted++;
        } else {
            $errors++;
            echo "❌ Erro ao inserir: $key - " . $insertStmt->error . "\n";
        }
    }
}

$checkStmt->close();
$insertStmt->close();
$updateStmt->close();
$conn->close();

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "  RESULTADO\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "✅ Inseridas: $inserted\n";
echo "🔄 Atualizadas: $updated\n";
echo "⏭️  Ignoradas (já existem): $skipped\n";
echo "❌ Erros: $errors\n";
echo "═══════════════════════════════════════════════════════════════\n";
?>
