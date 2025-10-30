<?php

/**
 * list_posts() 함수 테스트
 *
 * 이 테스트는 lib/post/post.crud.php의 list_posts() 함수를 검증합니다.
 *
 * 테스트 항목:
 * 1. 기본 호출 (필터 없음)
 * 2. category 필터
 * 3. user_id 필터
 * 4. visibility 필터 (문자열)
 * 5. visibility 필터 (배열)
 * 6. limit 및 offset 테스트
 * 7. page 파라미터 테스트 (자동 offset 계산)
 * 8. PostListModel 객체 반환 검증
 * 9. 페이지네이션 정보 검증
 */

// init.php 포함 (모든 라이브러리와 설정 로드)
include __DIR__ . '/../../init.php';

// 테스트 함수 로드
if (!function_exists('login_as_test_user')) {
    include __DIR__ . '/../../lib/test/test.functions.php';
}

echo "🧪 list_posts() 함수 테스트\n";
echo "======================================================================\n\n";

try {
    // ========================================================================
    // 테스트 준비: 테스트 사용자로 로그인
    // ========================================================================
    echo "🔧 테스트 준비: 테스트 사용자로 로그인\n";
    login_as_test_user('banana');
    $user = login();
    echo "   로그인된 사용자: {$user->first_name} {$user->last_name} (ID: {$user->id})\n\n";

    // ========================================================================
    // 테스트 준비: 테스트용 게시글 생성
    // ========================================================================
    echo "🔧 테스트 준비: 테스트용 게시글 생성\n";
    echo "   ⚠️  중요: create_post()에서 visibility가 'public'이 아니면 category가 visibility 값으로 자동 설정됩니다.\n\n";

    $test_posts = [];

    // public 게시글 3개 생성
    for ($i = 1; $i <= 3; $i++) {
        $post = create_post([
            'category' => 'test-category',
            'title' => "Test Post Public $i",
            'content' => "Test content for public post $i",
            'visibility' => 'public'
        ]);
        $test_posts[] = $post->id;
        echo "   게시글 생성: ID={$post->id}, category=test-category, visibility=public\n";
    }

    // friends 게시글 2개 생성 (category는 자동으로 'friends'로 설정됨)
    for ($i = 1; $i <= 2; $i++) {
        $post = create_post([
            'title' => "Test Post Friends $i",
            'content' => "Test content for friends post $i",
            'visibility' => 'friends'
        ]);
        $test_posts[] = $post->id;
        echo "   게시글 생성: ID={$post->id}, category=friends (자동 설정), visibility=friends\n";
    }

    // private 게시글 1개 생성 (category는 자동으로 'private'로 설정됨)
    $post = create_post([
        'title' => 'Test Post Private',
        'content' => 'Test content for private post',
        'visibility' => 'private'
    ]);
    $test_posts[] = $post->id;
    echo "   게시글 생성: ID={$post->id}, category=private (자동 설정), visibility=private\n";

    // 다른 카테고리 게시글 1개 생성
    $post = create_post([
        'category' => 'other-category',
        'title' => 'Test Post Other Category',
        'content' => 'Test content for other category',
        'visibility' => 'public'
    ]);
    $test_posts[] = $post->id;
    echo "   게시글 생성: ID={$post->id}, category=other-category, visibility=public\n\n";

    // ========================================================================
    // 테스트 1: 기본 호출 (필터 없음)
    // ========================================================================
    echo "🧪 테스트 1: 기본 호출 (필터 없음)\n";

    $result = list_posts([]);

    if ($result instanceof PostListModel) {
        echo "   ✅ PostListModel 객체 반환 성공\n";
        echo "   - 조회된 게시글 수: " . count($result->posts) . "\n";
        echo "   - 전체 게시글 수: {$result->total}\n";
        echo "   - 현재 페이지: {$result->page}\n";
        echo "   - 페이지당 게시글 수: {$result->per_page}\n";
    } else {
        echo "   ❌ PostListModel 객체가 반환되지 않음\n";
        exit(1);
    }

    // posts 배열이 PostModel 객체들로 구성되어 있는지 확인
    if (count($result->posts) > 0 && $result->posts[0] instanceof PostModel) {
        echo "   ✅ posts 배열이 PostModel 객체들로 구성됨\n";
    } else {
        echo "   ❌ posts 배열이 PostModel 객체들로 구성되지 않음\n";
        exit(1);
    }

    echo "\n";

    // ========================================================================
    // 테스트 2: category 필터
    // ========================================================================
    echo "🧪 테스트 2: category 필터 테스트\n";

    $result = list_posts(['category' => 'test-category', 'user_id' => $user->id]);

    if ($result instanceof PostListModel) {
        echo "   ✅ PostListModel 객체 반환 성공\n";
        echo "   - test-category 게시글 수: " . count($result->posts) . "\n";

        // 모든 게시글이 test-category인지 확인
        $all_test_category = true;
        foreach ($result->posts as $post) {
            if ($post->category !== 'test-category') {
                $all_test_category = false;
                break;
            }
        }

        if ($all_test_category && count($result->posts) >= 3) {
            echo "   ✅ 모든 게시글이 test-category 카테고리임 (public 게시글만)\n";
        } else {
            echo "   ❌ category 필터가 제대로 동작하지 않음\n";
            echo "   - 예상: 3개 이상, 실제: " . count($result->posts) . "개\n";
            exit(1);
        }
    } else {
        echo "   ❌ PostListModel 객체가 반환되지 않음\n";
        exit(1);
    }

    echo "\n";

    // ========================================================================
    // 테스트 3: user_id 필터
    // ========================================================================
    echo "🧪 테스트 3: user_id 필터 테스트\n";

    $result = list_posts(['user_id' => $user->id]);

    if ($result instanceof PostListModel) {
        echo "   ✅ PostListModel 객체 반환 성공\n";
        echo "   - 사용자 {$user->id}의 게시글 수: " . count($result->posts) . "\n";

        // 모든 게시글이 해당 사용자의 것인지 확인
        $all_user_posts = true;
        foreach ($result->posts as $post) {
            if ($post->user_id !== $user->id) {
                $all_user_posts = false;
                break;
            }
        }

        if ($all_user_posts) {
            echo "   ✅ 모든 게시글이 해당 사용자의 것임\n";
        } else {
            echo "   ❌ user_id 필터가 제대로 동작하지 않음\n";
            exit(1);
        }
    } else {
        echo "   ❌ PostListModel 객체가 반환되지 않음\n";
        exit(1);
    }

    echo "\n";

    // ========================================================================
    // 테스트 4: visibility 필터 (문자열)
    // ========================================================================
    echo "🧪 테스트 4: visibility 필터 (문자열) 테스트\n";

    $result = list_posts(['visibility' => 'public', 'category' => 'test-category', 'user_id' => $user->id]);

    if ($result instanceof PostListModel) {
        echo "   ✅ PostListModel 객체 반환 성공\n";
        echo "   - public 게시글 수: " . count($result->posts) . "\n";

        // 모든 게시글이 public인지 확인
        $all_public = true;
        foreach ($result->posts as $post) {
            if ($post->visibility !== 'public') {
                $all_public = false;
                break;
            }
        }

        if ($all_public && count($result->posts) >= 3) {
            echo "   ✅ 모든 게시글이 public visibility임\n";
        } else {
            echo "   ❌ visibility 필터(문자열)가 제대로 동작하지 않음\n";
            echo "   - 예상: 3개 이상, 실제: " . count($result->posts) . "개\n";
            exit(1);
        }
    } else {
        echo "   ❌ PostListModel 객체가 반환되지 않음\n";
        exit(1);
    }

    echo "\n";

    // ========================================================================
    // 테스트 5: visibility 필터 (배열)
    // ========================================================================
    echo "🧪 테스트 5: visibility 필터 (배열) 테스트\n";

    $result = list_posts([
        'visibility' => ['public', 'friends'],
        'user_id' => $user->id
    ]);

    if ($result instanceof PostListModel) {
        echo "   ✅ PostListModel 객체 반환 성공\n";
        echo "   - public + friends 게시글 수: " . count($result->posts) . "\n";

        // 모든 게시글이 public 또는 friends인지 확인
        $all_valid = true;
        $public_count = 0;
        $friends_count = 0;
        foreach ($result->posts as $post) {
            if ($post->visibility === 'public') {
                $public_count++;
            } elseif ($post->visibility === 'friends') {
                $friends_count++;
            } else {
                $all_valid = false;
                break;
            }
        }

        if ($all_valid && $public_count >= 4 && $friends_count >= 2) {
            echo "   ✅ 모든 게시글이 public 또는 friends visibility임\n";
            echo "   - public: {$public_count}개 (test-category 3 + other-category 1)\n";
            echo "   - friends: {$friends_count}개\n";
        } else {
            echo "   ❌ visibility 필터(배열)가 제대로 동작하지 않음\n";
            echo "   - 예상: public 4개 이상 + friends 2개 이상\n";
            echo "   - 실제: public {$public_count}개 + friends {$friends_count}개\n";
            exit(1);
        }
    } else {
        echo "   ❌ PostListModel 객체가 반환되지 않음\n";
        exit(1);
    }

    echo "\n";

    // ========================================================================
    // 테스트 6: limit 및 offset 테스트
    // ========================================================================
    echo "🧪 테스트 6: limit 및 offset 테스트\n";

    $result = list_posts([
        'user_id' => $user->id,
        'limit' => 2,
        'offset' => 0
    ]);

    if ($result instanceof PostListModel) {
        echo "   ✅ PostListModel 객체 반환 성공\n";
        echo "   - 조회된 게시글 수: " . count($result->posts) . "\n";

        if (count($result->posts) === 2) {
            echo "   ✅ limit=2 설정이 올바르게 동작함\n";
        } else {
            echo "   ❌ limit 설정이 제대로 동작하지 않음\n";
            exit(1);
        }

        // offset=2로 다시 조회
        $result2 = list_posts([
            'user_id' => $user->id,
            'limit' => 2,
            'offset' => 2
        ]);

        if (count($result2->posts) === 2) {
            echo "   ✅ offset=2 설정이 올바르게 동작함\n";
        } else {
            echo "   ❌ offset 설정이 제대로 동작하지 않음\n";
            exit(1);
        }
    } else {
        echo "   ❌ PostListModel 객체가 반환되지 않음\n";
        exit(1);
    }

    echo "\n";

    // ========================================================================
    // 테스트 7: page 파라미터 테스트 (자동 offset 계산)
    // ========================================================================
    echo "🧪 테스트 7: page 파라미터 테스트 (자동 offset 계산)\n";

    // 페이지 1 조회 (offset=0)
    $result_page1 = list_posts([
        'user_id' => $user->id,
        'page' => 1,
        'limit' => 2
    ]);

    if ($result_page1 instanceof PostListModel) {
        echo "   ✅ 페이지 1 조회 성공\n";
        echo "   - 조회된 게시글 수: " . count($result_page1->posts) . "\n";
        echo "   - 현재 페이지: {$result_page1->page}\n";

        if ($result_page1->page === 1 && count($result_page1->posts) === 2) {
            echo "   ✅ page=1 설정이 올바르게 동작함\n";
        } else {
            echo "   ❌ page=1 설정이 제대로 동작하지 않음\n";
            exit(1);
        }
    } else {
        echo "   ❌ PostListModel 객체가 반환되지 않음\n";
        exit(1);
    }

    // 페이지 2 조회 (offset=2)
    $result_page2 = list_posts([
        'user_id' => $user->id,
        'page' => 2,
        'limit' => 2
    ]);

    if ($result_page2 instanceof PostListModel) {
        echo "   ✅ 페이지 2 조회 성공\n";
        echo "   - 조회된 게시글 수: " . count($result_page2->posts) . "\n";
        echo "   - 현재 페이지: {$result_page2->page}\n";

        if ($result_page2->page === 2 && count($result_page2->posts) === 2) {
            echo "   ✅ page=2 설정이 올바르게 동작함 (자동 offset 계산)\n";
        } else {
            echo "   ❌ page=2 설정이 제대로 동작하지 않음\n";
            exit(1);
        }

        // 페이지 1과 페이지 2의 게시글이 다른지 확인
        if ($result_page1->posts[0]->id !== $result_page2->posts[0]->id) {
            echo "   ✅ 페이지 1과 페이지 2의 게시글이 서로 다름 (offset이 올바르게 계산됨)\n";
        } else {
            echo "   ❌ 페이지 1과 페이지 2의 게시글이 같음 (offset 계산 오류)\n";
            exit(1);
        }
    } else {
        echo "   ❌ PostListModel 객체가 반환되지 않음\n";
        exit(1);
    }

    echo "\n";

    // ========================================================================
    // 테스트 8: PostListModel 페이지네이션 정보 검증
    // ========================================================================
    echo "🧪 테스트 8: PostListModel 페이지네이션 정보 검증\n";

    $result = list_posts([
        'user_id' => $user->id,
        'page' => 1,
        'limit' => 3
    ]);

    if ($result instanceof PostListModel) {
        echo "   ✅ PostListModel 객체 반환 성공\n";
        echo "   - 전체 게시글 수: {$result->total}\n";
        echo "   - 현재 페이지: {$result->page}\n";
        echo "   - 페이지당 게시글 수: {$result->per_page}\n";
        echo "   - 전체 페이지 수: {$result->total_pages}\n";
        echo "   - 조회된 게시글 수: " . count($result->posts) . "\n";

        // 페이지네이션 정보 검증
        if ($result->total >= 7 && $result->page === 1 && $result->per_page === 3) {
            echo "   ✅ 페이지네이션 정보가 올바름\n";
        } else {
            echo "   ❌ 페이지네이션 정보가 올바르지 않음\n";
            exit(1);
        }

        // 전체 페이지 수 계산 검증
        $expected_total_pages = ceil($result->total / $result->per_page);
        if ($result->total_pages === $expected_total_pages) {
            echo "   ✅ 전체 페이지 수가 올바르게 계산됨\n";
        } else {
            echo "   ❌ 전체 페이지 수 계산 오류\n";
            echo "   - 예상: $expected_total_pages, 실제: {$result->total_pages}\n";
            exit(1);
        }
    } else {
        echo "   ❌ PostListModel 객체가 반환되지 않음\n";
        exit(1);
    }

    echo "\n";

    // ========================================================================
    // 테스트 9: comments 포함 여부 확인
    // ========================================================================
    echo "🧪 테스트 9: comments 포함 여부 확인\n";

    $result = list_posts([
        'user_id' => $user->id,
        'limit' => 1
    ]);

    if ($result instanceof PostListModel && count($result->posts) > 0) {
        $post = $result->posts[0];
        echo "   ✅ PostListModel 객체 반환 성공\n";
        echo "   - 게시글 ID: {$post->id}\n";

        if (property_exists($post, 'comments') && is_array($post->comments)) {
            echo "   ✅ comments 배열이 포함되어 있음\n";
            echo "   - comments 수: " . count($post->comments) . "\n";
        } else {
            echo "   ❌ comments 배열이 포함되어 있지 않음\n";
            exit(1);
        }
    } else {
        echo "   ❌ PostListModel 객체가 반환되지 않음\n";
        exit(1);
    }

    echo "\n";

    // ========================================================================
    // 테스트 완료: 생성된 테스트 게시글 삭제
    // ========================================================================
    echo "🧹 테스트 완료: 생성된 테스트 게시글 삭제\n";

    foreach ($test_posts as $post_id) {
        try {
            delete_post(['id' => $post_id]);
            echo "   게시글 삭제: ID=$post_id\n";
        } catch (Throwable $e) {
            echo "   ⚠️  게시글 삭제 실패: ID=$post_id (이미 삭제되었거나 존재하지 않음)\n";
        }
    }

    echo "\n======================================================================\n";
    echo "🎉 모든 list_posts() 함수 테스트 통과!\n";

} catch (Throwable $e) {
    echo "\n❌ 예외 발생: " . $e->getMessage() . "\n";
    echo "   파일: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "   스택 트레이스:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
