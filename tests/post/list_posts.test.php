<?php

/**
 * list_posts() 함수 테스트 (PostListModel 반환)
 *
 * 이 테스트는 게시글 목록 조회 함수의 다양한 필터 기능과 PostListModel 반환을 테스트합니다.
 *
 * 실행 방법:
 * php tests/post/list_posts.test.php
 */

// 1단계: init.php 로드
include __DIR__ . '/../../init.php';

// 2단계: 테스트 헬퍼 함수 로드
if (!function_exists('login_as_test_user')) {
    include __DIR__ . '/../../lib/test/test.functions.php';
}

// 3단계: 테스트 사용자로 로그인
login_as_test_user('apple');

echo "\n";
echo "========================================\n";
echo "📝 list_posts() 함수 테스트 시작\n";
echo "========================================\n\n";

// 테스트 카운터
$testCount = 0;
$passedCount = 0;

// ========================================================================
// 테스트 1: PostListModel 객체 반환 확인
// ========================================================================
$testCount++;
echo "테스트 {$testCount}: PostListModel 객체 반환 확인\n";

$postList = list_posts();

if ($postList instanceof PostListModel) {
    echo "   ✅ PostListModel 객체 반환 성공\n";
    echo "   ✅ 게시글 수: " . $postList->count() . "개, 전체: " . $postList->total . "개\n";
    echo "   ✅ 페이지: " . $postList->page . "/" . $postList->total_pages . "\n";
    $passedCount++;
} else {
    echo "   ❌ PostListModel 객체 반환 실패\n";
}

echo "\n";

// ========================================================================
// 테스트 2: 카테고리 필터
// ========================================================================
$testCount++;
echo "테스트 {$testCount}: 카테고리 필터 (discussion)\n";

$postList = list_posts(['category' => 'discussion']);

if ($postList instanceof PostListModel) {
    $allDiscussion = true;
    foreach ($postList->posts as $post) {
        if ($post->category !== 'discussion') {
            $allDiscussion = false;
            break;
        }
    }

    if ($allDiscussion) {
        echo "   ✅ discussion 카테고리 필터 성공 (총 " . $postList->count() . "개)\n";
        $passedCount++;
    } else {
        echo "   ❌ discussion 카테고리 필터 실패 (다른 카테고리 포함)\n";
    }
} else {
    echo "   ❌ 카테고리 필터 실패 (PostListModel이 아님)\n";
}

echo "\n";

// ========================================================================
// 테스트 3: 사용자 ID 필터
// ========================================================================
$testCount++;
echo "테스트 {$testCount}: 사용자 ID 필터\n";

$user = login();
$postList = list_posts(['user_id' => $user->id]);

if ($postList instanceof PostListModel) {
    $allSameUser = true;
    foreach ($postList->posts as $post) {
        if ($post->user_id !== $user->id) {
            $allSameUser = false;
            break;
        }
    }

    if ($allSameUser) {
        echo "   ✅ 사용자 ID 필터 성공 (총 " . $postList->count() . "개)\n";
        $passedCount++;
    } else {
        echo "   ❌ 사용자 ID 필터 실패 (다른 사용자 포함)\n";
    }
} else {
    echo "   ❌ 사용자 ID 필터 실패 (PostListModel이 아님)\n";
}

echo "\n";

// ========================================================================
// 테스트 4: LIMIT 필터
// ========================================================================
$testCount++;
echo "테스트 {$testCount}: LIMIT 필터 (최대 5개)\n";

$postList = list_posts(['limit' => 5]);

if ($postList instanceof PostListModel && $postList->count() <= 5) {
    echo "   ✅ LIMIT 필터 성공 (총 " . $postList->count() . "개)\n";
    echo "   ✅ per_page: " . $postList->per_page . "\n";
    $passedCount++;
} else {
    echo "   ❌ LIMIT 필터 실패\n";
}

echo "\n";

// ========================================================================
// 테스트 5: 페이지네이션 정보 확인
// ========================================================================
$testCount++;
echo "테스트 {$testCount}: 페이지네이션 정보 확인\n";

$postList = list_posts(['limit' => 3, 'page' => 2]);

if ($postList instanceof PostListModel) {
    echo "   ✅ 현재 페이지: " . $postList->page . "\n";
    echo "   ✅ 페이지당 게시글 수: " . $postList->per_page . "\n";
    echo "   ✅ 전체 게시글 수: " . $postList->total . "\n";
    echo "   ✅ 전체 페이지 수: " . $postList->total_pages . "\n";
    $passedCount++;
} else {
    echo "   ❌ 페이지네이션 정보 실패\n";
}

echo "\n";

// ========================================================================
// 테스트 6: 페이지네이션 헬퍼 메서드
// ========================================================================
$testCount++;
echo "테스트 {$testCount}: 페이지네이션 헬퍼 메서드\n";

$postList = list_posts(['limit' => 3, 'page' => 2]);

if ($postList instanceof PostListModel) {
    $hasNext = $postList->hasNextPage();
    $hasPrev = $postList->hasPreviousPage();
    $nextPage = $postList->getNextPage();
    $prevPage = $postList->getPreviousPage();

    echo "   ✅ hasNextPage(): " . ($hasNext ? 'true' : 'false') . "\n";
    echo "   ✅ hasPreviousPage(): " . ($hasPrev ? 'true' : 'false') . "\n";
    echo "   ✅ getNextPage(): " . ($nextPage ?? 'null') . "\n";
    echo "   ✅ getPreviousPage(): " . ($prevPage ?? 'null') . "\n";

    if ($hasPrev && $prevPage === 1) {
        $passedCount++;
    }
} else {
    echo "   ❌ 페이지네이션 헬퍼 메서드 실패\n";
}

echo "\n";

// ========================================================================
// 테스트 7: toArray() 메서드
// ========================================================================
$testCount++;
echo "테스트 {$testCount}: toArray() 메서드\n";

$postList = list_posts(['limit' => 2]);
$array = $postList->toArray();

if (isset($array['posts']) && isset($array['pagination'])) {
    echo "   ✅ toArray() 반환 구조 확인\n";
    echo "   ✅ posts 키 존재: " . (isset($array['posts']) ? 'true' : 'false') . "\n";
    echo "   ✅ pagination 키 존재: " . (isset($array['pagination']) ? 'true' : 'false') . "\n";
    $passedCount++;
} else {
    echo "   ❌ toArray() 메서드 실패\n";
}

echo "\n";

// ========================================================================
// 테스트 8: isEmpty() 메서드
// ========================================================================
$testCount++;
echo "테스트 {$testCount}: isEmpty() 메서드\n";

$postList = list_posts(['limit' => 1]);
$isEmpty = $postList->isEmpty();

if ($isEmpty === false && $postList->count() > 0) {
    echo "   ✅ isEmpty() 메서드 성공 (비어있지 않음)\n";
    $passedCount++;
} else if ($isEmpty === true && $postList->count() === 0) {
    echo "   ✅ isEmpty() 메서드 성공 (비어있음)\n";
    $passedCount++;
} else {
    echo "   ❌ isEmpty() 메서드 실패\n";
}

echo "\n";

// ========================================================================
// 테스트 결과 요약
// ========================================================================
echo "========================================\n";
echo "📊 테스트 결과 요약:\n";
echo "   ✅ 성공: {$passedCount}개\n";
echo "   ❌ 실패: " . ($testCount - $passedCount) . "개\n";
echo "   📈 성공률: " . round(($passedCount / $testCount) * 100, 2) . "%\n";
echo "========================================\n";

// 모든 테스트 통과 시 exit(0), 실패 시 exit(1)
if ($passedCount === $testCount) {
    echo "\n✅ 모든 테스트 통과!\n\n";
    exit(0);
} else {
    echo "\n❌ 일부 테스트 실패!\n\n";
    exit(1);
}
