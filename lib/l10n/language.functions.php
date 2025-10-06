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
 * 사용자 언어 설정을 가져온다. 우선순위는 다음과 같다:
 * 1) HTTP 파라메타로 ?lang=en 과 같이 언어 정보가 넘어오면, 해당 언어로 설정
 * 2) 쿠키에 language_code 가 있으면, 해당 언어로 설정
 * 3) 쿠키에 lang 이 있으면, 해당 언어로 설정 (이전 버전 호환성)
 * 4) 브라우저 언어를 사용
 * 5) 위 모든 경우에 해당하지 않으면 영어를 기본값으로 사용
 * @return string
 */
function get_user_lang(): string
{
    // Supported languages
    $supportedLanguages = ['en', 'ko', 'ja', 'zh'];

    // 1) 'lang' 에 HTTP GET 파라메타가 있으면, 해당 값을 우선으로 사용
    $lang = isset($_GET['lang']) ? $_GET['lang'] : '';
    if (!empty($lang) && in_array($lang, $supportedLanguages)) {
        return $lang;
    }

    // 2) 쿠키에 language_code 가 있으면, 해당 언어를 사용
    $lang = isset($_COOKIE['language_code']) ? $_COOKIE['language_code'] : '';
    if (!empty($lang) && in_array($lang, $supportedLanguages)) {
        return $lang;
    }

    // 3) 쿠키에 lang 이 있으면, 해당 언어를 사용 (이전 버전 호환성)
    $lang = isset($_COOKIE['lang']) ? $_COOKIE['lang'] : '';
    if (!empty($lang) && in_array($lang, $supportedLanguages)) {
        return $lang;
    }

    // 4) 브라우저 언어를 사용
    $lang = get_browser_language();
    if (!empty($lang) && in_array($lang, $supportedLanguages)) {
        return $lang;
    }

    // 5) 위 모든 경우에 해당하지 않으면 영어를 기본값으로 사용
    return 'en';
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
 * two-letter language code sent by the browser. It only returns supported languages
 * (ko, en, ja, zh) and defaults to 'en' for unsupported languages.
 *
 * @return string Two-letter language code (ko, en, ja, or zh)
 */
function get_browser_language(): string
{
    // Supported languages in sonub
    $supportedLanguages = ['ko', 'en', 'ja', 'zh'];

    // Check if HTTP_ACCEPT_LANGUAGE header exists
    if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        return 'en'; // Default to English if no language header
    }

    $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'];

    // Parse the accept language header
    // Format: en-US,en;q=0.9,ko;q=0.8,ja;q=0.7
    $languages = explode(',', $acceptLanguage);

    if (empty($languages)) {
        return 'en'; // Default to English if parsing fails
    }

    // Get the first (preferred) language
    $firstLanguage = trim($languages[0]);

    // Remove quality value if present (e.g., "en;q=0.9")
    if (strpos($firstLanguage, ';') !== false) {
        $firstLanguage = explode(';', $firstLanguage)[0];
    }

    // Extract only the two-letter language code (e.g., "en-US" -> "en")
    if (strlen($firstLanguage) > 2 && strpos($firstLanguage, '-') !== false) {
        $firstLanguage = explode('-', $firstLanguage)[0];
    }

    // Ensure we have exactly two characters
    $firstLanguage = substr($firstLanguage, 0, 2);

    // Check if the language is supported, otherwise default to English
    if (in_array($firstLanguage, $supportedLanguages)) {
        return $firstLanguage;
    }

    return 'en'; // Default to English for unsupported languages
}

/**
 * 사용자가 선택한 언어를 쿠키에 저장하는 API 함수
 *
 * @param array $params - API 파라미터, 'language_code' 키를 포함해야 함
 * @return array - 성공 또는 실패 응답
 */
function select_language(array $params): array
{
    // Supported languages
    $supportedLanguages = ['en', 'ko', 'ja', 'zh'];

    // 언어 코드 파라미터 확인
    if (!isset($params['language_code'])) {
        return error('missing-language-code', 'Language code is required');
    }

    $languageCode = $params['language_code'];

    // 지원하는 언어인지 확인
    if (!in_array($languageCode, $supportedLanguages)) {
        return error('unsupported-language', 'Language code is not supported');
    }

    // 쿠키 설정 (1년간 유효)
    $expiry = time() + (365 * 24 * 60 * 60); // 1년
    $path = '/';
    $domain = ''; // 현재 도메인
    $secure = false; // HTTPS인 경우만 true
    $httponly = true; // HTTP 통신에서만 접근 가능

    $result = setcookie('language_code', $languageCode, [
        'expires' => $expiry,
        'path' => $path,
        'domain' => $domain,
        'secure' => $secure,
        'httponly' => $httponly,
        'samesite' => 'Lax'
    ]);

    if (!$result) {
        return error('cookie-set-failed', 'Failed to set language cookie');
    }

    // 즉시 반영을 위해 $_COOKIE 변수도 설정
    $_COOKIE['language_code'] = $languageCode;

    return [
        'success' => true,
        'message' => 'Language saved successfully',
        'language_code' => $languageCode
    ];
}
