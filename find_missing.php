<?php
require __DIR__.'/vendor/autoload.php';
\$app = require_once __DIR__.'/bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Translation;

// Read untranslated strings
\$json = file_get_contents(__DIR__ . '/all_untranslated_strings.json');
\$strings = json_decode(\$json, true);

echo "Checking " . count(\$strings) . " strings...\n\n";

\$missing = [];
\$found = [];

foreach (\$strings as \$string) {
    \$existing = Translation::where('lang', 'pt')->where('lang_key', \$string)->first();
    
    if (!\$existing) {
        \$missing[] = \$string;
    } else {
        \$found[] = \$string;
    }
}

echo "=== Results ===\n";
echo "Found (already translated): " . count(\$found) . "\n";
echo "Missing (need translation): " . count(\$missing) . "\n\n";

if (count(\$missing) > 0) {
    echo "First 50 missing translations:\n";
    foreach (array_slice(\$missing, 0, 50) as \$m) {
        echo "  - \$m\n";
    }
    
    // Save missing to file
    file_put_contents(__DIR__ . '/truly_missing.json', json_encode(\$missing, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "\nSaved " . count(\$missing) . " missing strings to truly_missing.json\n";
}
