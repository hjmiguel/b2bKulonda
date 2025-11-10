<?php

// Bootstrap Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Starting translation analysis...\n\n";

// Read all strings from the file
$strings = file('backend_all_strings.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$totalStrings = count($strings);

echo "Total strings to check: $totalStrings\n";

// Get all existing PT translations
echo "Fetching existing PT translations from database...\n";
$existingTranslations = DB::table('translations')
    ->where('lang', 'pt')
    ->pluck('lang_value', 'lang_key')
    ->toArray();

$totalExisting = count($existingTranslations);
echo "Total PT translations in database: $totalExisting\n\n";

// Analyze each string
$alreadyTranslated = [];
$missingTranslations = [];
$notActuallyTranslated = []; // lang_value == lang_key

echo "Analyzing strings...\n";

$processed = 0;
foreach ($strings as $string) {
    $string = trim($string);
    if (empty($string)) continue;
    
    // Remove quotes if present
    $string = trim($string, "'\"");
    
    $processed++;
    if ($processed % 200 == 0) {
        echo "Processed $processed/$totalStrings...\n";
    }
    
    if (isset($existingTranslations[$string])) {
        // Check if it's actually translated or just copied
        if ($existingTranslations[$string] === $string) {
            $notActuallyTranslated[] = $string;
        } else {
            $alreadyTranslated[] = $string;
        }
    } else {
        $missingTranslations[] = $string;
    }
}

echo "\nAnalysis complete!\n";
echo "=".str_repeat("=", 70)."\n";

// Generate statistics
$alreadyCount = count($alreadyTranslated);
$notTranslatedCount = count($notActuallyTranslated);
$missingCount = count($missingTranslations);
$needsCount = $notTranslatedCount + $missingCount;

$stats = [
    'total_strings' => $totalStrings,
    'already_translated' => $alreadyCount,
    'not_actually_translated' => $notTranslatedCount,
    'completely_missing' => $missingCount,
    'needs_translation' => $needsCount
];

echo "\nSTATISTICS:\n";
echo "=".str_repeat("=", 70)."\n";
echo "Total strings in backend_all_strings.txt: {$totalStrings}\n";
echo "Already translated (proper PT): {$alreadyCount} (" . round(($alreadyCount/$totalStrings)*100, 2) . "%)\n";
echo "Not actually translated (PT=EN): {$notTranslatedCount} (" . round(($notTranslatedCount/$totalStrings)*100, 2) . "%)\n";
echo "Completely missing from DB: {$missingCount} (" . round(($missingCount/$totalStrings)*100, 2) . "%)\n";
echo "=".str_repeat("=", 70)."\n";
echo "TOTAL NEEDING TRANSLATION: {$needsCount} (" . round(($needsCount/$totalStrings)*100, 2) . "%)\n";
echo "=".str_repeat("=", 70)."\n\n";

// Prepare output data
$needsTranslation = array_merge($notActuallyTranslated, $missingTranslations);

$output = [
    'statistics' => $stats,
    'already_translated' => $alreadyTranslated,
    'not_actually_translated' => $notActuallyTranslated,
    'completely_missing' => $missingTranslations,
    'all_needing_translation' => $needsTranslation
];

// Save to JSON file
file_put_contents('translation_analysis.json', json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "Full analysis saved to: translation_analysis.json\n\n";

// Show first 30 untranslated strings
echo "FIRST 30 STRINGS NEEDING TRANSLATION:\n";
echo "=".str_repeat("=", 70)."\n";
$first30 = array_slice($needsTranslation, 0, 30);
foreach ($first30 as $index => $str) {
    echo ($index + 1) . ". $str\n";
}

if (count($needsTranslation) > 30) {
    echo "\n... and " . (count($needsTranslation) - 30) . " more\n";
}

echo "\nDone!\n";
