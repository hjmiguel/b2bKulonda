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

    echo "=== Corrigindo tabela orders ===\n\n";
    
    // Verificar se colunas existem
    \$stmt = \$pdo->query("SHOW COLUMNS FROM orders LIKE \"manual_payment\"");
    \$hasManualPayment = \$stmt->rowCount() > 0;
    
    \$stmt = \$pdo->query("SHOW COLUMNS FROM orders LIKE \"manual_payment_data\"");
    \$hasManualPaymentData = \$stmt->rowCount() > 0;
    
    if (!\$hasManualPayment) {
        echo "Adicionando coluna manual_payment...\n";
        \$pdo->exec("ALTER TABLE orders ADD COLUMN manual_payment int(1) DEFAULT 0 AFTER payment_type");
        echo "✅ Coluna manual_payment adicionada!\n";
    } else {
        echo "✅ Coluna manual_payment já existe\n";
    }
    
    if (!\$hasManualPaymentData) {
        echo "Adicionando coluna manual_payment_data...\n";
        \$pdo->exec("ALTER TABLE orders ADD COLUMN manual_payment_data longtext DEFAULT NULL AFTER manual_payment");
        echo "✅ Coluna manual_payment_data adicionada!\n";
    } else {
        echo "✅ Coluna manual_payment_data já existe\n";
    }
    
    echo "\n✅ Tabela orders corrigida com sucesso!\n";
    echo "\n📍 Agora teste novamente: https://app.kulonda.ao/checkout/payment\n";
    
} catch (Exception \$e) {
    echo "❌ Erro: " . \$e->getMessage() . "\n";
}
