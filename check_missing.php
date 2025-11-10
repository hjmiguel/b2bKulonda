<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Translation;

$strings = json_decode(file_get_contents(__DIR__ . "/all_untranslated_strings.json"), true);

echo "Checking " . count($strings) . " strings...\n";

$missing = [];
$count = 0;

foreach ($strings as $string) {
    $exists = Translation::where("lang", "pt")->where("lang_key", $string)->exists();
    if (!$exists) {
        $missing[] = $string;
    }
    $count++;
    if ($count % 100 == 0) {
        echo "Processed $count...\n";
    }
}

echo "\n=== Results ===\n";
echo "Total: " . count($strings) . "\n";
echo "Found: " . (count($strings) - count($missing)) . "\n";
echo "Missing: " . count($missing) . "\n\n";

if (count($missing) > 0) {
    echo "First 30 missing:\n";
    foreach (array_slice($missing, 0, 30) as $m) {
        echo "  - $m\n";
    }
    file_put_contents(__DIR__ . "/truly_missing.json", json_encode($missing, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "\nSaved " . count($missing) . " missing to truly_missing.json\n";
} else {
    echo "All strings are already translated!\n";
}
