<?php

$data = json_decode(file_get_contents('translation_analysis.json'), true);

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║       BACKEND TRANSLATION ANALYSIS REPORT - PORTUGUESE (PT-PT)        ║\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

echo "OVERVIEW:\n";
echo "══════════════════════════════════════════════════════════════════════════\n";
echo "Total Backend Strings Analyzed: {['statistics']['total_strings']}\n";
echo "Database Info: translations table (lang='pt')\n";
echo "Source File: backend_all_strings.txt\n";
echo "\n";

echo "TRANSLATION STATUS:\n";
echo "══════════════════════════════════════════════════════════════════════════\n";

$total = $data['statistics']['total_strings'];
$translated = $data['statistics']['already_translated'];
$notTranslated = $data['statistics']['not_actually_translated'];
$missing = $data['statistics']['completely_missing'];
$needsWork = $data['statistics']['needs_translation'];

$translatedPct = round(($translated/$total)*100, 2);
$notTranslatedPct = round(($notTranslated/$total)*100, 2);
$missingPct = round(($missing/$total)*100, 2);
$needsWorkPct = round(($needsWork/$total)*100, 2);

echo sprintf("✓ Already Translated:          %4d  (%6.2f%%)  [GOOD]\n", $translated, $translatedPct);
echo sprintf("✗ Exists but Not Translated:   %4d  (%6.2f%%)  [PT=EN]\n", $notTranslated, $notTranslatedPct);
echo sprintf("✗ Missing from Database:       %4d  (%6.2f%%)  [NEW]\n", $missing, $missingPct);
echo "══════════════════════════════════════════════════════════════════════════\n";
echo sprintf("⚠ TOTAL NEEDS TRANSLATION:     %4d  (%6.2f%%)\n", $needsWork, $needsWorkPct);
echo "══════════════════════════════════════════════════════════════════════════\n";
echo "\n";

echo "CATEGORY BREAKDOWN:\n";
echo "══════════════════════════════════════════════════════════════════════════\n";

// Analyze categories
$categories = [
    'technical' => 0,
    'ui_labels' => 0,
    'descriptions' => 0,
    'actions' => 0,
    'other' => 0
];

$technicalKeywords = ['px', 'mode', 'key', 'id', 'api', 'firebase', 'recaptcha'];
$actionKeywords = ['add', 'edit', 'delete', 'create', 'update', 'save', 'cancel'];
$descKeywords = ['description', 'info', 'detail', 'about', 'widget'];

foreach ($data['all_needing_translation'] as $str) {
    $lower = strtolower($str);
    $categorized = false;
    
    foreach ($technicalKeywords as $kw) {
        if (strpos($lower, $kw) !== false) {
            $categories['technical']++;
            $categorized = true;
            break;
        }
    }
    
    if (!$categorized) {
        foreach ($actionKeywords as $kw) {
            if (strpos($lower, $kw) !== false) {
                $categories['actions']++;
                $categorized = true;
                break;
            }
        }
    }
    
    if (!$categorized) {
        foreach ($descKeywords as $kw) {
            if (strpos($lower, $kw) !== false) {
                $categories['descriptions']++;
                $categorized = true;
                break;
            }
        }
    }
    
    if (!$categorized) {
        if (strlen($str) < 20 && preg_match('/^[A-Z]/', $str)) {
            $categories['ui_labels']++;
        } else {
            $categories['other']++;
        }
    }
}

echo "Technical terms (API keys, modes, etc.): {$categories['technical']}\n";
echo "UI Labels (buttons, menus, etc.):       {$categories['ui_labels']}\n";
echo "Descriptions & Info text:               {$categories['descriptions']}\n";
echo "Action words (Add, Edit, etc.):         {$categories['actions']}\n";
echo "Other:                                   {$categories['other']}\n";
echo "\n";

echo "FILES GENERATED:\n";
echo "══════════════════════════════════════════════════════════════════════════\n";
echo "1. translation_analysis.json - Full analysis with all data\n";
echo "   - Contains all 1,701 strings needing translation\n";
echo "   - Contains 177 already translated strings\n";
echo "   - Size: " . filesize('translation_analysis.json') . " bytes\n";
echo "\n";

echo "NEXT STEPS:\n";
echo "══════════════════════════════════════════════════════════════════════════\n";
echo "1. Review the 7 strings where PT=EN (should be translated)\n";
echo "2. Process the 1,694 completely missing translations\n";
echo "3. Use translation_analysis.json as input for batch translation\n";
echo "4. Consider prioritizing by category (UI labels first, then actions)\n";
echo "\n";

echo "STRINGS REQUIRING ATTENTION (PT=EN):\n";
echo "══════════════════════════════════════════════════════════════════════════\n";
foreach ($data['not_actually_translated'] as $i => $str) {
    echo ($i+1) . ". $str\n";
}
echo "\n";

echo "Report generated on: " . date('Y-m-d H:i:s') . "\n";
echo "══════════════════════════════════════════════════════════════════════════\n";
