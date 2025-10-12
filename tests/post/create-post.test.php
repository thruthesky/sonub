<?php

/**
 * create_post() 함수 Unit 테스트
 *
 * 테스트 목적:
 * - create_post() 함수가 올바르게 게시글을 생성하는지 검증
 * - 필수 입력값 검증 (title, content, category)
 * - Unix timestamp 저장 확인
 * - PostModel 객체 반환 확인
 * - toArray() 메서드 동작 확인
 */

// init.php 포함 - 모든 라이브러리, 함수, DB 설정 로드
include __DIR__ . '/../../init.php';

echo "🧪 create_post() 함수 Unit 테스트\n";
echo str_repeat("=", 70) . "\n\n";

// ============================================================================
// 테스트 1: 필수 입력값 누락 시 null 반환 확인
// ============================================================================
echo "🧪 테스트 1: 필수 입력값 누락 시 null 반환 확인\n";

// 1-1: title 누락
$result = create_post([
    'content' => '내용만 있음',
    'category' => 'discussion'
]);
if ($result === null) {
    echo "   ✅ title 누락 시 null 반환 (정상)\n";
} else {
    echo "   ❌ 실패: title 누락 시에도 객체 반환됨\n";
    exit(1);
}

// 1-2: content 누락
$result = create_post([
    'title' => '제목만 있음',
    'category' => 'discussion'
]);
if ($result === null) {
    echo "   ✅ content 누락 시 null 반환 (정상)\n";
} else {
    echo "   ❌ 실패: content 누락 시에도 객체 반환됨\n";
    exit(1);
}

// 1-3: category 누락
$result = create_post([
    'title' => '제목만 있음',
    'content' => '내용만 있음'
]);
if ($result === null) {
    echo "   ✅ category 누락 시 null 반환 (정상)\n";
} else {
    echo "   ❌ 실패: category 누락 시에도 객체 반환됨\n";
    exit(1);
}

// 1-4: 빈 문자열 입력
$result = create_post([
    'title' => '',
    'content' => '',
    'category' => ''
]);
if ($result === null) {
    echo "   ✅ 빈 문자열 입력 시 null 반환 (정상)\n";
} else {
    echo "   ❌ 실패: 빈 문자열 입력 시에도 객체 반환됨\n";
    exit(1);
}

// 1-5: 공백만 있는 문자열 입력
$result = create_post([
    'title' => '   ',
    'content' => '   ',
    'category' => '   '
]);
if ($result === null) {
    echo "   ✅ 공백만 있는 문자열 입력 시 null 반환 (정상)\n\n";
} else {
    echo "   ❌ 실패: 공백만 있는 문자열 입력 시에도 객체 반환됨\n";
    exit(1);
}

// ============================================================================
// 테스트 2: 정상적인 게시글 생성
// ============================================================================
echo "🧪 테스트 2: 정상적인 게시글 생성\n";

$test_title = 'Unit 테스트 게시글 ' . time();
$test_content = '이것은 create_post() 함수 테스트입니다. Unix timestamp: ' . time();
$test_category = 'test';

$post = create_post([
    'title' => $test_title,
    'content' => $test_content,
    'category' => $test_category
]);

if ($post === null) {
    echo "   ❌ 실패: 정상 입력값인데 null 반환됨\n";
    exit(1);
}

if (!($post instanceof PostModel)) {
    echo "   ❌ 실패: PostModel 객체가 아님\n";
    exit(1);
}

echo "   ✅ PostModel 객체 반환 성공\n";
echo "   📝 생성된 게시글 ID: {$post->id}\n\n";

// ============================================================================
// 테스트 3: PostModel 객체 데이터 검증
// ============================================================================
echo "🧪 테스트 3: PostModel 객체 데이터 검증\n";

// 3-1: ID 존재 확인
if ($post->id > 0) {
    echo "   ✅ ID 생성됨: {$post->id}\n";
} else {
    echo "   ❌ 실패: ID가 0 이하\n";
    exit(1);
}

// 3-2: title 확인
if ($post->title === $test_title) {
    echo "   ✅ title 일치: {$post->title}\n";
} else {
    echo "   ❌ 실패: title 불일치 (예상: {$test_title}, 실제: {$post->title})\n";
    exit(1);
}

// 3-3: content 확인
if ($post->content === $test_content) {
    echo "   ✅ content 일치\n";
} else {
    echo "   ❌ 실패: content 불일치\n";
    exit(1);
}

// 3-4: category 확인
if ($post->category === $test_category) {
    echo "   ✅ category 일치: {$post->category}\n";
} else {
    echo "   ❌ 실패: category 불일치 (예상: {$test_category}, 실제: {$post->category})\n";
    exit(1);
}

// 3-5: created_at이 Unix timestamp인지 확인
if (is_int($post->created_at) && $post->created_at > 0) {
    echo "   ✅ created_at은 Unix timestamp (정수): {$post->created_at}\n";
} else {
    echo "   ❌ 실패: created_at이 Unix timestamp가 아님\n";
    exit(1);
}

// 3-6: updated_at이 Unix timestamp인지 확인
if (is_int($post->updated_at) && $post->updated_at > 0) {
    echo "   ✅ updated_at은 Unix timestamp (정수): {$post->updated_at}\n";
} else {
    echo "   ❌ 실패: updated_at이 Unix timestamp가 아님\n";
    exit(1);
}

// 3-7: created_at과 updated_at이 같은지 확인 (생성 시점이므로 같아야 함)
if ($post->created_at === $post->updated_at) {
    echo "   ✅ created_at과 updated_at이 동일 (생성 시점)\n\n";
} else {
    echo "   ⚠️  경고: created_at과 updated_at이 다름 (차이: " . ($post->updated_at - $post->created_at) . "초)\n\n";
}

// ============================================================================
// 테스트 4: toArray() 메서드 동작 확인
// ============================================================================
echo "🧪 테스트 4: toArray() 메서드 동작 확인\n";

// toArray() 메서드 존재 확인
if (!method_exists($post, 'toArray')) {
    echo "   ❌ 실패: PostModel에 toArray() 메서드가 없음\n";
    exit(1);
}

echo "   ✅ toArray() 메서드 존재\n";

// toArray() 실행
$postArray = $post->toArray();

if (!is_array($postArray)) {
    echo "   ❌ 실패: toArray()가 배열을 반환하지 않음\n";
    exit(1);
}

echo "   ✅ toArray()가 배열 반환\n";

// 배열에 필수 키가 있는지 확인
$required_keys = ['id', 'title', 'content', 'category', 'created_at', 'updated_at'];
foreach ($required_keys as $key) {
    if (!isset($postArray[$key])) {
        echo "   ❌ 실패: toArray() 결과에 '{$key}' 키가 없음\n";
        exit(1);
    }
}

echo "   ✅ 모든 필수 키 존재 (" . implode(', ', $required_keys) . ")\n";

// 배열 값과 객체 값이 일치하는지 확인
if ($postArray['id'] === $post->id &&
    $postArray['title'] === $post->title &&
    $postArray['content'] === $post->content &&
    $postArray['category'] === $post->category &&
    $postArray['created_at'] === $post->created_at &&
    $postArray['updated_at'] === $post->updated_at) {
    echo "   ✅ 배열 값과 객체 값 일치\n\n";
} else {
    echo "   ❌ 실패: 배열 값과 객체 값 불일치\n";
    exit(1);
}

// ============================================================================
// 테스트 5: JSON 인코딩 확인
// ============================================================================
echo "🧪 테스트 5: JSON 인코딩 확인 (API 응답 준비)\n";

$json = json_encode($postArray, JSON_UNESCAPED_UNICODE);

if ($json === false) {
    echo "   ❌ 실패: JSON 인코딩 실패\n";
    exit(1);
}

echo "   ✅ JSON 인코딩 성공\n";

// JSON 디코딩 확인
$decoded = json_decode($json, true);
if ($decoded === null) {
    echo "   ❌ 실패: JSON 디코딩 실패\n";
    exit(1);
}

echo "   ✅ JSON 디코딩 성공\n";

// 디코딩된 데이터와 원본 데이터 일치 확인
if ($decoded['id'] === $postArray['id'] &&
    $decoded['title'] === $postArray['title'] &&
    $decoded['content'] === $postArray['content'] &&
    $decoded['category'] === $postArray['category']) {
    echo "   ✅ 디코딩된 데이터와 원본 데이터 일치\n\n";
} else {
    echo "   ❌ 실패: 디코딩된 데이터와 원본 데이터 불일치\n";
    exit(1);
}

// ============================================================================
// 테스트 6: 생성된 게시글 조회 확인
// ============================================================================
echo "🧪 테스트 6: 생성된 게시글 조회 확인 (get_post_by_id)\n";

$retrieved_post = get_post_by_id($post->id);

if ($retrieved_post === null) {
    echo "   ❌ 실패: 생성된 게시글을 조회할 수 없음\n";
    exit(1);
}

echo "   ✅ 게시글 조회 성공\n";

if ($retrieved_post->id === $post->id &&
    $retrieved_post->title === $post->title &&
    $retrieved_post->content === $post->content &&
    $retrieved_post->category === $post->category) {
    echo "   ✅ 조회된 데이터와 원본 데이터 일치\n\n";
} else {
    echo "   ❌ 실패: 조회된 데이터와 원본 데이터 불일치\n";
    exit(1);
}

// ============================================================================
// 테스트 완료
// ============================================================================
echo str_repeat("=", 70) . "\n";
echo "✅ 모든 테스트 통과!\n\n";

echo "📊 테스트 요약:\n";
echo "   - 필수 입력값 검증: ✅ 통과\n";
echo "   - 게시글 생성: ✅ 통과\n";
echo "   - PostModel 데이터 검증: ✅ 통과\n";
echo "   - toArray() 메서드: ✅ 통과\n";
echo "   - JSON 인코딩/디코딩: ✅ 통과\n";
echo "   - 게시글 조회: ✅ 통과\n\n";

echo "📝 생성된 테스트 게시글 정보:\n";
echo "   - ID: {$post->id}\n";
echo "   - Title: {$post->title}\n";
echo "   - Category: {$post->category}\n";
echo "   - Created: " . date('Y-m-d H:i:s', $post->created_at) . " (Unix: {$post->created_at})\n";
echo "   - Updated: " . date('Y-m-d H:i:s', $post->updated_at) . " (Unix: {$post->updated_at})\n\n";
