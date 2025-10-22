<?php
/**
 * list_users() 함수의 이름 검색 기능 테스트
 *
 * 요구사항: display_name이 정확히 일치하는 사용자만 검색해야 함
 *
 * 실행 방법:
 * php tests/user/list_users_search.test.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../init.php';

echo "========================================\n";
echo "list_users() 이름 검색 기능 테스트\n";
echo "========================================\n\n";

// 테스트 카운터
$test_count = 0;
$passed = 0;
$failed = 0;

/**
 * 테스트 함수
 */
function test_assert(bool $condition, string $message): void
{
    global $test_count, $passed, $failed;
    $test_count++;
    if ($condition) {
        echo "✓ 테스트 {$test_count}: {$message}\n";
        $passed++;
    } else {
        echo "✗ 테스트 {$test_count}: {$message}\n";
        $failed++;
    }
}

$pdo = pdo();

// ============================================================================
// 테스트 데이터 준비
// ============================================================================
echo "테스트 데이터 준비 중...\n";
echo "----------------------------------------\n";

// 테스트용 사용자 생성
$now = time();
$test_users = [
    ['firebase_uid' => 'test_search_1', 'first_name' => '길동', 'last_name' => '홍'],
    ['firebase_uid' => 'test_search_2', 'first_name' => '길동', 'last_name' => '김'],
    ['firebase_uid' => 'test_search_3', 'first_name' => '순신', 'last_name' => '이'],
    ['firebase_uid' => 'test_search_4', 'first_name' => '세종대왕', 'last_name' => ''],
    ['firebase_uid' => 'test_search_5', 'first_name' => '감찬', 'last_name' => '강'],
];

// 기존 테스트 사용자 삭제
$pdo->exec("DELETE FROM users WHERE firebase_uid LIKE 'test_search_%'");

// 테스트 사용자 생성
$stmt = $pdo->prepare("INSERT INTO users (firebase_uid, first_name, last_name, middle_name, created_at, updated_at, birthday, gender, photo_url) VALUES (?, ?, ?, ?, ?, ?, 0, '', '')");
foreach ($test_users as $user) {
    $stmt->execute([$user['firebase_uid'], $user['first_name'], $user['last_name'], '', $now, $now]);
}

echo "✓ 테스트 사용자 5명 생성 완료\n\n";

// ============================================================================
// 테스트 1: 정확히 일치하는 이름 검색
// ============================================================================
echo "테스트 1: 정확히 일치하는 이름 검색\n";
echo "----------------------------------------\n";

$result = list_users(['name' => '길동', 'per_page' => 100]);
$found = false;
foreach ($result['users'] as $user) {
    if ($user['first_name'] === '길동' && $user['last_name'] === '홍') {
        $found = true;
        break;
    }
}
test_assert($found, "검색어 '길동'으로 '홍길동(first_name=길동, last_name=홍)' 사용자를 찾을 수 있어야 함");

echo "\n";

// ============================================================================
// 테스트 2: 부분 일치는 검색되지 않아야 함 (핵심 테스트)
// ============================================================================
echo "테스트 2: 부분 일치는 검색되지 않아야 함\n";
echo "----------------------------------------\n";

// 성(last_name)으로만 검색하면 안됨
$result = list_users(['name' => '홍', 'per_page' => 100]);
$found_hong = false;
foreach ($result['users'] as $user) {
    if ($user['first_name'] === '길동' && $user['last_name'] === '홍') {
        $found_hong = true;
        break;
    }
}
test_assert(!$found_hong, "검색어 '홍'(성만)으로는 '홍길동' 사용자를 찾을 수 없어야 함 (first_name 기준 검색)");

// 부분 문자열로 검색하면 안됨
$result = list_users(['name' => '길', 'per_page' => 100]);
$found_any = false;
foreach ($result['users'] as $user) {
    if (($user['first_name'] === '길동' && $user['last_name'] === '홍') ||
        ($user['first_name'] === '길동' && $user['last_name'] === '김')) {
        $found_any = true;
        break;
    }
}
test_assert(!$found_any, "검색어 '길'(부분)으로는 '길동' 사용자들을 찾을 수 없어야 함 (부분 일치 금지)");

echo "\n";

// ============================================================================
// 테스트 3: 여러 사용자 정확히 일치 검색
// ============================================================================
echo "테스트 3: 여러 사용자 정확히 일치 검색\n";
echo "----------------------------------------\n";

$result = list_users(['name' => '순신', 'per_page' => 100]);
$found = false;
foreach ($result['users'] as $user) {
    if ($user['first_name'] === '순신' && $user['last_name'] === '이') {
        $found = true;
        break;
    }
}
test_assert($found, "검색어 '순신'으로 '이순신(first_name=순신, last_name=이)' 사용자를 찾을 수 있어야 함");

$result = list_users(['name' => '세종대왕', 'per_page' => 100]);
$found = false;
foreach ($result['users'] as $user) {
    if ($user['first_name'] === '세종대왕') {
        $found = true;
        break;
    }
}
test_assert($found, "검색어 '세종대왕'으로 '세종대왕(first_name=세종대왕)' 사용자를 찾을 수 있어야 함");

echo "\n";

// ============================================================================
// 테스트 4: 검색 결과 없음
// ============================================================================
echo "테스트 4: 검색 결과 없음\n";
echo "----------------------------------------\n";

$result = list_users(['name' => '존재하지않는이름', 'per_page' => 100]);
$has_test_user = false;
foreach ($result['users'] as $user) {
    if (strpos($user['firebase_uid'], 'test_search_') === 0) {
        $has_test_user = true;
        break;
    }
}
test_assert(!$has_test_user, "검색어 '존재하지않는이름'으로는 테스트 사용자를 찾을 수 없어야 함");

echo "\n";

// ============================================================================
// 테스트 5: 영문 이름 정확히 일치 검색 (대소문자는 MySQL 설정에 따름)
// ============================================================================
echo "테스트 5: 영문 이름 정확히 일치 검색\n";
echo "----------------------------------------\n";

// 영문 이름 테스트 사용자 추가
$pdo->exec("DELETE FROM users WHERE firebase_uid LIKE 'test_search_en_%'");
$stmt->execute(['test_search_en_1', 'John', 'Smith', '', $now, $now]);
$stmt->execute(['test_search_en_2', 'jane', 'doe', '', $now, $now]);

// 정확히 일치하는 first_name으로 검색
$result = list_users(['name' => 'John', 'per_page' => 100]);
$found = false;
foreach ($result['users'] as $user) {
    if ($user['first_name'] === 'John' && $user['last_name'] === 'Smith') {
        $found = true;
        break;
    }
}
test_assert($found, "검색어 'John'으로 'John Smith(first_name=John, last_name=Smith)' 사용자를 찾을 수 있어야 함");

// last_name으로만 검색하면 안됨
$result = list_users(['name' => 'Smith', 'per_page' => 100]);
$found = false;
foreach ($result['users'] as $user) {
    if ($user['first_name'] === 'John' && $user['last_name'] === 'Smith') {
        $found = true;
        break;
    }
}
test_assert(!$found, "검색어 'Smith'(성만)로는 'John Smith' 사용자를 찾을 수 없어야 함 (first_name 기준 검색)");

echo "\n";

// ============================================================================
// 테스트 정리
// ============================================================================
echo "테스트 정리 중...\n";
echo "----------------------------------------\n";

// 테스트 데이터 삭제
$pdo->exec("DELETE FROM users WHERE firebase_uid LIKE 'test_search_%'");

echo "✓ 테스트 데이터 정리 완료\n\n";

// ============================================================================
// 테스트 결과 요약
// ============================================================================
echo "========================================\n";
echo "테스트 결과 요약\n";
echo "========================================\n";
echo "총 테스트: {$test_count}개\n";
echo "통과: {$passed}개\n";
echo "실패: {$failed}개\n";
echo "========================================\n";

if ($failed === 0) {
    echo "✅ 모든 테스트 통과!\n";
    exit(0);
} else {
    echo "❌ {$failed}개 테스트 실패\n";
    exit(1);
}
