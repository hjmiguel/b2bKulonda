<?php
\$envFile = __DIR__ . "/.env";
\$envContent = file_get_contents(\$envFile);
preg_match("/DB_DATABASE=(.+)/", \$envContent, \$dbName);
preg_match("/DB_USERNAME=(.+)/", \$envContent, \$dbUser);
preg_match("/DB_PASSWORD=(.+)/", \$envContent, \$dbPass);
preg_match("/DB_HOST=(.+)/", \$envContent, \$dbHost);

\$database = trim(str_replace("\"", "", \$dbName[1] ?? ""));
\$username = trim(str_replace("\"", "", \$dbUser[1] ?? ""));
\$password = trim(str_replace("\"", "", \$dbPass[1] ?? ""));
\$host = trim(str_replace("\"", "", \$dbHost[1] ?? "localhost"));

try {
    \$pdo = new PDO("mysql:host=\$host;dbname=\$database;charset=utf8mb4", \$username, \$password);
    \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== Estrutura da tabela orders ===\n\n";
    
    \$stmt = \$pdo->query("DESCRIBE orders");
    \$columns = \$stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Colunas encontradas:\n";
    foreach (\$columns as \$col) {
        echo "  - {\$col[\"Field\"]} ({\$col[\"Type\"]})\n";
    }
    
    echo "\n=== Verificando coluna manual_payment ===\n";
    \$hasManualPayment = false;
    \$hasManualPaymentData = false;
    
    foreach (\$columns as \$col) {
        if (\$col["Field"] === "manual_payment") {
            \$hasManualPayment = true;
        }
        if (\$col["Field"] === "manual_payment_data") {
            \$hasManualPaymentData = true;
        }
    }
    
    if (\$hasManualPayment) {
        echo "✅ Coluna manual_payment existe\n";
    } else {
        echo "❌ Coluna manual_payment NÃO existe\n";
    }
    
    if (\$hasManualPaymentData) {
        echo "✅ Coluna manual_payment_data existe\n";
    } else {
        echo "❌ Coluna manual_payment_data NÃO existe\n";
    }
    
} catch (Exception \$e) {
    echo "❌ Erro: " . \$e->getMessage() . "\n";
}
