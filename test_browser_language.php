<?php
// Include the language functions file
include_once 'lib/l10n/language.functions.php';

// Test cases for different HTTP_ACCEPT_LANGUAGE headers
$testCases = [
    'en-US,en;q=0.9,ko;q=0.8',
    'ko-KR,ko;q=0.9,en;q=0.8',
    'ja,en-US;q=0.9,en;q=0.8',
    'zh-CN,zh;q=0.9,en;q=0.8',
    'en',
    'ko',
    '',
    null
];

echo "Testing Browser Language Detection Function\n";
echo "==========================================\n\n";

foreach ($testCases as $index => $testHeader) {
    echo "Test Case " . ($index + 1) . ": ";

    // Set the test header
    if ($testHeader === null) {
        unset($_SERVER['HTTP_ACCEPT_LANGUAGE']);
        echo "No HTTP_ACCEPT_LANGUAGE header\n";
    } else {
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = $testHeader;
        echo "HTTP_ACCEPT_LANGUAGE: " . ($testHeader ?: '(empty)') . "\n";
    }

    // Call our function
    $result = get_browser_language();
    echo "Detected Language: $result\n";
    echo "----------------------------------------\n";
}

echo "\nTest completed. The function should correctly extract the primary language code from each test case.\n";
