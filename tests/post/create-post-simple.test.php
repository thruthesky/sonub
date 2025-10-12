<?php

/**
 * create_post() 함수 간단 Unit 테스트
 */

include __DIR__ . '/../../init.php';

echo "🧪 create_post() 간단 테스트\n";
echo str_repeat("=", 70) . "\n\n";

// 테스트 1: 필수값 누락 (user_id 없음)
echo "🧪 테스트 1: user_id 누락 시 null 반환\n";
$result = create_post([
    'title' => '제목',
    'content' => '내용',
    'category' => 'test'
]);
echo $result === null ? "   ✅ 통과\n\n" : "   ❌ 실패\n\n";

// 테스트 2: 정상적인 게시글 생성
echo "🧪 테스트 2: 정상 게시글 생성\n";
$post = create_post([
    'title' => 'Test Post ' . time(),
    'content' => 'Test Content ' . time(),
    'category' => 'test',
    'user_id' => 1
]);

if ($post && $post instanceof PostModel) {
    echo "   ✅ PostModel 객체 반환\n";
    echo "   📝 ID: {$post->id}\n";
    echo "   📝 Title: {$post->title}\n";
    echo "   📝 Category: {$post->category}\n";
    echo "   📝 User ID: {$post->user_id}\n";
    echo "   📝 Created: {$post->created_at} (" . date('Y-m-d H:i:s', $post->created_at) . ")\n\n";
} else {
    echo "   ❌ 실패: null 반환\n\n";
    exit(1);
}

// 테스트 3: toArray() 메서드
echo "🧪 테스트 3: toArray() 메서드\n";
if (method_exists($post, 'toArray')) {
    $arr = $post->toArray();
    if (is_array($arr) && isset($arr['id'], $arr['title'], $arr['content'])) {
        echo "   ✅ toArray() 정상 작동\n\n";
    } else {
        echo "   ❌ 실패: toArray() 결과 이상\n\n";
        exit(1);
    }
} else {
    echo "   ❌ 실패: toArray() 메서드 없음\n\n";
    exit(1);
}

echo "✅ 모든 테스트 통과!\n";
