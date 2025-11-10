<?php
define("LARAVEL_START", microtime(true));
require __DIR__ . "/vendor/autoload.php";
\$app = require_once __DIR__ . "/bootstrap/app.php";
\$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
\$kernel->bootstrap();

echo "=== Limpando TODOS os caches ===\n\n";

try {
    // 1. Config cache
    Artisan::call("config:clear");
    echo "✅ Config cache cleared\n";
    
    // 2. Route cache
    Artisan::call("route:clear");
    echo "✅ Route cache cleared\n";
    
    // 3. View cache
    Artisan::call("view:clear");
    echo "✅ View cache cleared\n";
    
    // 4. Application cache
    Artisan::call("cache:clear");
    echo "✅ Application cache cleared\n";
    
    // 5. Clear compiled
    Artisan::call("clear-compiled");
    echo "✅ Compiled files cleared\n";
    
    // 6. Optimize clear
    Artisan::call("optimize:clear");
    echo "✅ Optimization cleared\n";
    
    echo "\n✅ TODOS os caches foram limpos!\n";
    echo "\n🔄 Tente acessar novamente: https://app.kulonda.ao/\n";
    
} catch (Exception \$e) {
    echo "❌ Erro: " . \$e->getMessage() . "\n";
}
