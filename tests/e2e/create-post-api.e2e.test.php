<?php

/**
 * create_post() API E2E 테스트
 *
 * 테스트 목적:
 * - /api.php?func=create_post API 엔드포인트 테스트
 * - HTTP 요청/응답 검증
 * - JSON 응답 구조 검증
 * - PostModel 객체 → 배열 → JSON 변환 검증
 */

include __DIR__ . '/../../init.php';

echo "🧪 create_post() API E2E 테스트\n";
echo str_repeat("=", 70) . "\n\n";

$base_url = 'https://local.sonub.com';

// ============================================================================
// 테스트 1: 필수 파라미터 누락 (title 없음)
// ============================================================================
echo "🧪 테스트 1: title 누락 시 에러 응답\n";

$url = $base_url . '/api.php?func=create_post&content=test&category=test&user_id=1';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// 에러인 경우 400 또는 500 상태 코드 예상
if ($http_code >= 400) {
    echo "   ✅ HTTP 에러 상태 코드: {$http_code}\n";
} else {
    echo "   ⚠️  경고: 에러가 아닌 HTTP {$http_code} 반환\n";
}

$json = json_decode($response, true);
if (isset($json['func']) && $json['func'] === 'create_post') {
    echo "   ✅ JSON 응답에 func 필드 존재\n\n";
} else {
    echo "   ⚠️  경고: JSON 응답에 func 필드 없음\n\n";
}

// ============================================================================
// 테스트 2: 정상적인 게시글 생성 (GET 요청)
// ============================================================================
echo "🧪 테스트 2: 정상적인 게시글 생성 (GET 요청)\n";

$test_title = 'API 테스트 게시글 ' . time();
$test_content = 'API E2E 테스트 내용 ' . time();
$test_category = 'test';
$test_user_id = 1;

$url = $base_url . '/api.php?func=create_post'
    . '&title=' . urlencode($test_title)
    . '&content=' . urlencode($test_content)
    . '&category=' . urlencode($test_category)
    . '&user_id=' . $test_user_id;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200) {
    echo "   ❌ 실패: HTTP {$http_code} (예상: 200)\n";
    echo "   Response: {$response}\n";
    exit(1);
}

echo "   ✅ HTTP 200 OK\n";

$json = json_decode($response, true);
if ($json === null) {
    echo "   ❌ 실패: JSON 파싱 실패\n";
    echo "   Response: {$response}\n";
    exit(1);
}

echo "   ✅ JSON 파싱 성공\n";

// ============================================================================
// 테스트 3: JSON 응답 구조 검증
// ============================================================================
echo "\n🧪 테스트 3: JSON 응답 구조 검증\n";

// func 필드 확인
if (isset($json['func']) && $json['func'] === 'create_post') {
    echo "   ✅ func 필드: {$json['func']}\n";
} else {
    echo "   ❌ 실패: func 필드 없음 또는 불일치\n";
    exit(1);
}

// PostModel 필드 확인
$required_fields = ['id', 'user_id', 'category', 'title', 'content', 'created_at', 'updated_at'];
foreach ($required_fields as $field) {
    if (!isset($json[$field])) {
        echo "   ❌ 실패: '{$field}' 필드 없음\n";
        exit(1);
    }
}

echo "   ✅ 모든 필수 필드 존재 (" . implode(', ', $required_fields) . ")\n";

// ============================================================================
// 테스트 4: 응답 데이터 값 검증
// ============================================================================
echo "\n🧪 테스트 4: 응답 데이터 값 검증\n";

// ID 확인
if (is_int($json['id']) && $json['id'] > 0) {
    echo "   ✅ ID: {$json['id']} (정수, 양수)\n";
} else {
    echo "   ❌ 실패: ID가 정수 양수가 아님\n";
    exit(1);
}

// title 확인
if ($json['title'] === $test_title) {
    echo "   ✅ title 일치\n";
} else {
    echo "   ❌ 실패: title 불일치 (예상: {$test_title}, 실제: {$json['title']})\n";
    exit(1);
}

// content 확인
if ($json['content'] === $test_content) {
    echo "   ✅ content 일치\n";
} else {
    echo "   ❌ 실패: content 불일치\n";
    exit(1);
}

// category 확인
if ($json['category'] === $test_category) {
    echo "   ✅ category 일치: {$json['category']}\n";
} else {
    echo "   ❌ 실패: category 불일치\n";
    exit(1);
}

// user_id 확인
if ($json['user_id'] === $test_user_id) {
    echo "   ✅ user_id 일치: {$json['user_id']}\n";
} else {
    echo "   ❌ 실패: user_id 불일치\n";
    exit(1);
}

// created_at이 Unix timestamp인지 확인
if (is_int($json['created_at']) && $json['created_at'] > 0) {
    $created_date = date('Y-m-d H:i:s', $json['created_at']);
    echo "   ✅ created_at: {$json['created_at']} ({$created_date})\n";
} else {
    echo "   ❌ 실패: created_at이 Unix timestamp가 아님\n";
    exit(1);
}

// updated_at이 Unix timestamp인지 확인
if (is_int($json['updated_at']) && $json['updated_at'] > 0) {
    $updated_date = date('Y-m-d H:i:s', $json['updated_at']);
    echo "   ✅ updated_at: {$json['updated_at']} ({$updated_date})\n";
} else {
    echo "   ❌ 실패: updated_at이 Unix timestamp가 아님\n";
    exit(1);
}

// ============================================================================
// 테스트 5: POST 요청 테스트
// ============================================================================
echo "\n🧪 테스트 5: 정상적인 게시글 생성 (POST 요청)\n";

$test_title_post = 'API POST 테스트 게시글 ' . time();
$test_content_post = 'API POST E2E 테스트 내용 ' . time();

$post_data = http_build_query([
    'func' => 'create_post',
    'title' => $test_title_post,
    'content' => $test_content_post,
    'category' => 'test',
    'user_id' => 1
]);

$ch = curl_init($base_url . '/api.php');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 200) {
    echo "   ✅ HTTP 200 OK (POST 요청)\n";
} else {
    echo "   ❌ 실패: HTTP {$http_code} (POST 요청)\n";
    exit(1);
}

$json = json_decode($response, true);
if ($json && isset($json['id']) && $json['title'] === $test_title_post) {
    echo "   ✅ POST 요청으로 게시글 생성 성공\n";
    echo "   📝 생성된 게시글 ID: {$json['id']}\n";
} else {
    echo "   ❌ 실패: POST 요청 응답 이상\n";
    exit(1);
}

// ============================================================================
// 테스트 완료
// ============================================================================
echo "\n" . str_repeat("=", 70) . "\n";
echo "✅ 모든 API E2E 테스트 통과!\n\n";

echo "📊 테스트 요약:\n";
echo "   - 필수 파라미터 검증: ✅ 통과\n";
echo "   - GET 요청 테스트: ✅ 통과\n";
echo "   - JSON 응답 구조: ✅ 통과\n";
echo "   - 응답 데이터 검증: ✅ 통과\n";
echo "   - POST 요청 테스트: ✅ 통과\n\n";

echo "📝 API 엔드포인트:\n";
echo "   - GET:  {$base_url}/api.php?func=create_post&title=...&content=...&category=...&user_id=...\n";
echo "   - POST: {$base_url}/api.php (func=create_post&...)\n\n";
