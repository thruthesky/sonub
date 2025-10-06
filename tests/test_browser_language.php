<?php
// Include the language functions file
include_once 'lib/l10n/language.functions.php';

// Test cases for different HTTP_ACCEPT_LANGUAGE headers with expected results
$testCases = [
    // Supported languages with various formats
    ['header' => 'en-US,en;q=0.9,ko;q=0.8', 'expected' => 'en', 'description' => 'English US with quality values'],
    ['header' => 'ko-KR,ko;q=0.9,en;q=0.8', 'expected' => 'ko', 'description' => 'Korean KR with quality values'],
    ['header' => 'ja,en-US;q=0.9,en;q=0.8', 'expected' => 'ja', 'description' => 'Japanese without region'],
    ['header' => 'zh-CN,zh;q=0.9,en;q=0.8', 'expected' => 'zh', 'description' => 'Chinese CN with quality values'],
    ['header' => 'en', 'expected' => 'en', 'description' => 'Simple English'],
    ['header' => 'ko', 'expected' => 'ko', 'description' => 'Simple Korean'],

    // Unsupported languages should default to 'en'
    ['header' => 'fr-FR,fr;q=0.9,en;q=0.8', 'expected' => 'en', 'description' => 'French should default to English'],
    ['header' => 'de-DE,de;q=0.9,en;q=0.8', 'expected' => 'en', 'description' => 'German should default to English'],
    ['header' => 'es-ES,es;q=0.9,en;q=0.8', 'expected' => 'en', 'description' => 'Spanish should default to English'],
    ['header' => 'ru-RU,ru;q=0.9,en;q=0.8', 'expected' => 'en', 'description' => 'Russian should default to English'],
    ['header' => 'pt-BR,pt;q=0.9,en;q=0.8', 'expected' => 'en', 'description' => 'Portuguese should default to English'],

    // Edge cases
    ['header' => '', 'expected' => 'en', 'description' => 'Empty header should default to English'],
    ['header' => null, 'expected' => 'en', 'description' => 'No header should default to English'],
    ['header' => 'invalid-format', 'expected' => 'en', 'description' => 'Invalid format should default to English'],
    ['header' => 'x-unknown,en;q=0.9', 'expected' => 'en', 'description' => 'Unknown language should default to English'],
];

echo "Testing Browser Language Detection Function\n";
echo "==========================================\n\n";

$passedTests = 0;
$totalTests = count($testCases);

foreach ($testCases as $index => $testCase) {
    echo "Test Case " . ($index + 1) . ": " . $testCase['description'] . "\n";

    // Set the test header
    if ($testCase['header'] === null) {
        unset($_SERVER['HTTP_ACCEPT_LANGUAGE']);
        echo "HTTP_ACCEPT_LANGUAGE: (not set)\n";
    } else {
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = $testCase['header'];
        echo "HTTP_ACCEPT_LANGUAGE: " . ($testCase['header'] ?: '(empty)') . "\n";
    }

    // Call our function
    $result = get_browser_language();
    $expected = $testCase['expected'];

    echo "Expected: $expected\n";
    echo "Actual: $result\n";

    // Verify the result is exactly 2 characters
    if (strlen($result) !== 2) {
        echo "❌ FAIL: Result is not exactly 2 characters\n";
    }
    // Verify the result matches expected
    elseif ($result === $expected) {
        echo "✅ PASS\n";
        $passedTests++;
    } else {
        echo "❌ FAIL: Result does not match expected\n";
    }

    echo "----------------------------------------\n";
}

echo "\nTest Summary: $passedTests/$totalTests tests passed\n";

if ($passedTests === $totalTests) {
    echo "🎉 All tests passed! The function correctly returns only two-letter language codes and defaults to 'en' for unsupported languages.\n";
} else {
    echo "❌ Some tests failed. Please check the implementation.\n";
}
