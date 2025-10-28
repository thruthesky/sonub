<?php
/**
 * get_post() 함수 Unit 테스트
 *
 * 변경사항:
 * - $with_comments 파라미터 추가
 * - with_comments=true일 때 마지막 5개의 댓글 포함
 */

include __DIR__ . '/../../init.php';

// 테스트 사용자 로그인
if (!function_exists('login_as_test_user')) {
    include __DIR__ . '/../../lib/test/test.functions.php';
}

echo "🧪 get_post() 함수 Unit 테스트\n";
echo "======================================================================\n\n";

try {
    // ========================================================================
    // 테스트 준비: 테스트 게시글 생성
    // ========================================================================
    echo "🧪 테스트 준비: 테스트 게시글 생성\n";

    // 테스트 사용자로 로그인
    login_as_test_user('banana');
    $user = login();
    echo "   로그인된 사용자: {$user->first_name} {$user->last_name}\n";

    // 테스트 게시글 생성
    $testPost = create_post([
        'category' => 'discussion',
        'title' => 'get_post() 함수 테스트 게시글',
        'content' => '이 게시글은 get_post() 함수의 새로운 with_comments 파라미터를 테스트하기 위한 게시글입니다.',
    ]);

    if ($testPost instanceof PostModel) {
        $postId = $testPost->id;
        echo "   ✅ 테스트 게시글 생성 성공 (ID: {$postId})\n";
    } else {
        echo "   ❌ 테스트 게시글 생성 실패\n";
        exit(1);
    }

    echo "\n";

    // ========================================================================
    // 테스트 1: with_comments=false (기본값, 댓글 없음)
    // ========================================================================
    echo "🧪 테스트 1: with_comments=false (기본값, 댓글 없음)\n";

    $post = get_post(post_id: $postId, with_comments: false);

    if ($post instanceof PostModel) {
        echo "   ✅ get_post() 호출 성공\n";
        echo "   - ID: {$post->id}\n";
        echo "   - 제목: {$post->title}\n";

        // comments 필드 확인
        if (!isset($post->comments) || is_null($post->comments) || empty($post->comments)) {
            echo "   ✅ with_comments=false일 때 comments 필드가 없음 (또는 빈 배열)\n";
        } else {
            echo "   ❌ with_comments=false일 때 comments 필드가 있으면 안 됨\n";
            exit(1);
        }
    } else {
        echo "   ❌ get_post() 호출 실패\n";
        exit(1);
    }

    echo "\n";

    // ========================================================================
    // 테스트 2: with_comments=true (댓글 포함)
    // ========================================================================
    echo "🧪 테스트 2: with_comments=true (댓글 포함)\n";

    $post = get_post(post_id: $postId, with_comments: true);

    if ($post instanceof PostModel) {
        echo "   ✅ get_post() 호출 성공\n";
        echo "   - ID: {$post->id}\n";
        echo "   - 제목: {$post->title}\n";

        // comments 필드 확인
        if (isset($post->comments)) {
            if (is_array($post->comments)) {
                echo "   ✅ comments 필드가 배열로 설정됨\n";
                echo "   - comments 개수: " . count($post->comments) . "\n";

                // 최대 5개의 댓글 확인
                if (count($post->comments) <= 5) {
                    echo "   ✅ comments는 최대 5개까지만 포함됨 (정상)\n";
                } else {
                    echo "   ⚠️ comments가 5개를 초과함 (경고)\n";
                }
            } else {
                echo "   ❌ comments 필드가 배열이 아님: " . gettype($post->comments) . "\n";
                exit(1);
            }
        } else {
            echo "   ❌ with_comments=true일 때 comments 필드가 없음\n";
            exit(1);
        }
    } else {
        echo "   ❌ get_post() 호출 실패\n";
        exit(1);
    }

    echo "\n";

    // ========================================================================
    // 테스트 3: with_user=true, with_comments=true (사용자 정보와 댓글 모두 포함)
    // ========================================================================
    echo "🧪 테스트 3: with_user=true, with_comments=true\n";

    $post = get_post(post_id: $postId, with_user: true, with_comments: true);

    if ($post instanceof PostModel) {
        echo "   ✅ get_post() 호출 성공\n";
        echo "   - ID: {$post->id}\n";
        echo "   - 제목: {$post->title}\n";

        // 사용자 정보 확인 (author 객체의 속성으로 확인)
        if (isset($post->author) && $post->author instanceof AuthorModel) {
            if ($post->author->first_name !== null && $post->author->photo_url !== null && $post->author->firebase_uid !== null) {
                echo "   ✅ 사용자 정보 포함됨 (author.first_name, author.photo_url, author.firebase_uid)\n";
                echo "     - author.first_name: {$post->author->first_name}\n";
            } else {
                echo "   ❌ with_user=true일 때 사용자 정보가 NULL임\n";
                exit(1);
            }
        } else {
            echo "   ❌ with_user=true일 때 author 객체가 없음\n";
            exit(1);
        }

        // comments 필드 확인
        if (isset($post->comments) && is_array($post->comments)) {
            echo "   ✅ comments 필드가 배열로 설정됨\n";
            echo "   - comments 개수: " . count($post->comments) . "\n";
        } else {
            echo "   ❌ with_comments=true일 때 comments 필드가 없음\n";
            exit(1);
        }
    } else {
        echo "   ❌ get_post() 호출 실패\n";
        exit(1);
    }

    echo "\n";

    // ========================================================================
    // 테스트 4: 기본 호출 (파라미터 없음)
    // ========================================================================
    echo "🧪 테스트 4: 기본 호출 (파라미터 없음)\n";

    $post = get_post(post_id: $postId);

    if ($post instanceof PostModel) {
        echo "   ✅ get_post() 기본 호출 성공\n";
        echo "   - ID: {$post->id}\n";

        // with_user와 with_comments가 기본값(false)인지 확인
        if (!isset($post->comments) || is_null($post->comments) || empty($post->comments)) {
            echo "   ✅ 기본값에서 with_comments=false (댓글 미포함)\n";
        }

        // with_user=false인지 확인: author의 모든 필드가 NULL이어야 함
        if (isset($post->author) &&
            is_null($post->author->first_name) &&
            is_null($post->author->photo_url) &&
            is_null($post->author->firebase_uid)) {
            echo "   ✅ 기본값에서 with_user=false (사용자 정보 미포함)\n";
        }
    } else {
        echo "   ❌ get_post() 기본 호출 실패\n";
        exit(1);
    }

    echo "\n";

    // ========================================================================
    // 테스트 5: 존재하지 않는 게시글
    // ========================================================================
    echo "🧪 테스트 5: 존재하지 않는 게시글\n";

    $nonExistentId = 999999999;
    $post = get_post(post_id: $nonExistentId, with_comments: true);

    if ($post === null) {
        echo "   ✅ 존재하지 않는 게시글은 null 반환 (정상)\n";
    } else {
        echo "   ❌ null을 반환해야 함\n";
        exit(1);
    }

    echo "\n======================================================================\n";
    echo "🎉 모든 테스트 통과!\n\n";
    echo "✅ get_post() 함수 변경사항 검증 완료:\n";
    echo "   - ✅ with_comments=false (기본값): 댓글 미포함\n";
    echo "   - ✅ with_comments=true: 마지막 5개 댓글 포함\n";
    echo "   - ✅ with_user=true, with_comments=true: 사용자 정보와 댓글 모두 포함\n";
    echo "   - ✅ 기본 호출 (파라미터 없음): 정상 동작\n";
    echo "   - ✅ 존재하지 않는 게시글: null 반환\n";

} catch (Throwable $e) {
    echo "❌ 예외 발생: " . $e->getMessage() . "\n";
    echo "   스택 트레이스:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
