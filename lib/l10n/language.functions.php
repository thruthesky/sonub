<?php

/**
 * Translate text.
 * 
 * @param array|string $texts - Translatable texts
 * @param array $replaces - Variables to replace in the text
 * 
 * @example
 * ```php
 * echo tr(['en' => 'Hello', 'ko' => '안녕하세요']);
 * <?= tr(['en' => 'Phone Number', 'ko' => '전화번호']) ?>
 * <?= tr(['en' => 'Hello, {name} {age}', 'ko' => '안녕하세요, {name} {age}'], ['name' => 'John', 'age' => 30]) ?>
 * ```
 */
function tr(array|string $texts, array $replaces = [])
{
    if (is_array($texts)) {
        // 사용자 언어 가져오기
        $lang = get_user_lang();

        // If the selected language is not available in the texts array, fallback to 'ko'
        if (!isset($texts[$lang])) {
            $lang = 'ko';
        }

        // If 'ko' is also not available, return the first available translation
        if (!isset($texts[$lang])) {
            // Get the first available translation
            $firstKey = array_key_first($texts);
            $found = $firstKey !== null ? $texts[$firstKey] : $texts;
        } else {
            $found = $texts[$lang];
        }
    } else {
        $found = $texts;
    }

    foreach ($replaces as $key => $value) {
        $found = str_replace('{' . $key . '}', $value, $found);
    }

    return $found;
}


/**
 * 사용자가 설정을 하지 않으면 무조건 한국어로 보인다. 즉, 영어 웹브라우저라도 한국어로 보이도록 한다.
 * 
 * 로직:
 * 1) HTTP 파라메타로 ?lang=en 과 같이 언어 정보가 넘어오면, 해당 언어로 설정. (참고: etc/boot.php 에서 쿠키에 저장한다)
 * 2) 쿠키에 lang 이 있으면, 해당 언어로 설정
 * 3) HTTP 파라메타 ?lang=xx 이 없고, 쿠키에도 언어 설정이 없으면, 브라우저 언어는 무시하고, 무조건 한국어로 설정
 * @return string 
 */
function get_user_lang(): string
{

    // Supported languages
    $supportedLanguages = ['en', 'ko', 'ja', 'zh'];

    // 'lang' 에 HTTP GET 파라메타가 있으면, 해당 값을 우선으로 사용
    $lang = isset($_GET['lang']) ? $_GET['lang'] : '';
    if (!empty($lang) && in_array($lang, $supportedLanguages)) {
        return $lang;
    }

    // 그렇지 않으면, 쿠키에 저장된 언어 설정을 사용
    $lang = isset($_COOKIE['lang']) ? $_COOKIE['lang'] : '';
    if (!empty($lang) && in_array($lang, $supportedLanguages)) {
        return $lang;
    }

    // 그렇지 않으면, 쿠키에 저장된 언어 설정을 사용

    return 'ko'; // Default to Korean if no valid language is set
}





/**
 * Translate multi-language text from user input based on the user's locale
 * 
 * @usage
 * - Use it on Family Site text fields that accept multi-language input
 * 
 * @param string $text - Multi-language text
 * - ex: "en: Hello World\nko: 안녕하세요\nja: こんにちは\nzh: 你好"
 * 
 * @return string
 * - Translated text
 * - or original text if translation not available
 * Ex) "Hello World"
 */
function trs(string $text): string
{
    $translations = langfy($text);

    if ($translations === null) {
        return $text;
    }

    return tr($translations);
}


/**
 * Parse multi-language text and return an associative array of translations
 * 
 * @param string $text - Multi-language text with format "lang_code: content"
 * @return array|null - Associative array of translations or null if no language codes found
 */
function langfy(string $text): ?array
{
    $translations = [];

    // Use multiline flag and match language codes at line start
    $currentLang = null;
    $currentContent = [];
    $hasAnyLanguage = false;

    $lines = explode("\n", $text);

    foreach ($lines as $line) {
        // Match language codes at the beginning of the line
        if (preg_match('/^(en|ko|ja|zh):\s*(.*)$/', $line, $match)) {
            $hasAnyLanguage = true;
            // Save previous language content if exists
            if ($currentLang !== null) {
                $translations[$currentLang] = trim(implode("\n", $currentContent));
            }

            // Start new language
            $currentLang = $match[1];
            $currentContent = [$match[2]];
        } else if ($currentLang !== null) {
            // Continue adding content to current language
            $currentContent[] = $line;
        }
    }

    // Save last language content
    if ($currentLang !== null) {
        $translations[$currentLang] = trim(implode("\n", $currentContent));
    }

    // Return null if no language codes were found
    if (!$hasAnyLanguage) {
        return null;
    }

    return $translations;
}

/**
 * Get the browser language code from HTTP_ACCEPT_LANGUAGE header
 *
 * This function parses the HTTP_ACCEPT_LANGUAGE header to extract the primary
 * language code sent by the browser. This is useful for debugging and development
 * to understand what language code the browser is using.
 *
 * @return string The browser language code (e.g., "en-US", "ko", "ja", "zh-CN")
 */
function get_browser_language(): string
{
    // Check if HTTP_ACCEPT_LANGUAGE header exists
    if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        return 'Not detected';
    }

    $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'];

    // Parse the accept language header
    // Format: en-US,en;q=0.9,ko;q=0.8,ja;q=0.7
    $languages = explode(',', $acceptLanguage);

    if (empty($languages)) {
        return 'Not detected';
    }

    // Get the first (preferred) language
    $firstLanguage = trim($languages[0]);

    // Remove quality value if present (e.g., "en;q=0.9")
    if (strpos($firstLanguage, ';') !== false) {
        $firstLanguage = explode(';', $firstLanguage)[0];
    }

    return $firstLanguage;
}
