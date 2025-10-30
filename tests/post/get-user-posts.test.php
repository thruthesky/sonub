<?php

/**
 * get_user_posts() 함수 테스트 / get_user_posts() Function Test
 *
 * 이 테스트는 lib/post/post.crud.php의 get_user_posts() 함수를 검증합니다.
 * This test validates the get_user_posts() function in lib/post/post.crud.php.
 *
 * 테스트 항목 / Test Items:
 * 1. user_id 파라미터 누락 시 에러 / Error when user_id parameter is missing
 * 2. 본인 게시글 조회 (모든 visibility) / View own posts (all visibility)
 * 3. 로그아웃 상태에서 타인 게시글 조회 (public만) / View others' posts when logged out (public only)
 * 4. 로그인 상태 + 친구 관계 (public + friends) / Logged in + friends (public + friends)
 * 5. 로그인 상태 + 친구 아님 (public만) / Logged in + not friends (public only)
 * 6. 차단 관계일 때 빈 목록 반환 / Return empty list when blocked
 */

// init.php 포함 (모든 라이브러리와 설정 로드)
include __DIR__ . '/../../init.php';

// 테스트 함수 로드
if (!function_exists('login_as_test_user')) {
    include __DIR__ . '/../../lib/test/test.functions.php';
}

echo "🧪 get_user_posts() 함수 테스트 / get_user_posts() Function Test\n";
echo "======================================================================\n\n";

try {
    // ========================================================================
    // 테스트 준비: 테스트 사용자 2명 로그인 및 게시글 생성
    // Test Setup: Create 2 test users and posts
    // ========================================================================
    echo "🔧 테스트 준비: 테스트 사용자 및 게시글 생성\n";
    echo "🔧 Test Setup: Creating test users and posts\n\n";

    // User A (banana) 로그인
    login_as_test_user('banana');
    $userA = login();
    echo "   User A 로그인 / User A logged in: {$userA->first_name} {$userA->last_name} (ID: {$userA->id})\n";

    // User A의 게시글 생성 (다양한 visibility)
    $userA_posts = [];

    // public 게시글 2개
    for ($i = 1; $i <= 2; $i++) {
        $post = create_post([
            'category' => 'test-user-posts',
            'title' => "User A Public Post $i",
            'content' => "Public content $i",
            'visibility' => 'public'
        ]);
        $userA_posts[] = $post->id;
        echo "   User A 게시글 생성 / Created: ID={$post->id}, visibility=public\n";
    }

    // friends 게시글 2개
    for ($i = 1; $i <= 2; $i++) {
        $post = create_post([
            'title' => "User A Friends Post $i",
            'content' => "Friends content $i",
            'visibility' => 'friends'
        ]);
        $userA_posts[] = $post->id;
        echo "   User A 게시글 생성 / Created: ID={$post->id}, visibility=friends\n";
    }

    // private 게시글 1개
    $post = create_post([
        'title' => 'User A Private Post',
        'content' => 'Private content',
        'visibility' => 'private'
    ]);
    $userA_posts[] = $post->id;
    echo "   User A 게시글 생성 / Created: ID={$post->id}, visibility=private\n\n";

    // ========================================================================
    // 테스트 1: user_id 파라미터 누락 시 에러
    // Test 1: Error when user_id parameter is missing
    // ========================================================================
    echo "🧪 테스트 1: user_id 파라미터 누락 시 에러\n";
    echo "🧪 Test 1: Error when user_id parameter is missing\n";
    echo "   ⚠️  Note: error() function outputs JSON/HTML in CLI - this is expected\n";

    try {
        @get_user_posts([]); // @ suppresses error warnings
        echo "\n   ❌ 에러가 발생하지 않음 (예상치 못한 동작)\n";
        echo "   ❌ No error occurred (unexpected behavior)\n";
        exit(1);
    } catch (ApiException $e) {
        echo "\n   [DEBUG] Exception caught: " . $e->getCode() . "\n";
        if ($e->getCode() === 'user-id-required') {
            echo "   ✅ 예상된 에러 발생: {$e->getMessage()}\n";
            echo "   ✅ Expected error occurred: {$e->getMessage()}\n";
        } else {
            echo "   ❌ 다른 에러 발생: {$e->getMessage()}\n";
            echo "   ❌ Different error occurred: {$e->getMessage()}\n";
            exit(1);
        }
    } catch (Throwable $e) {
        echo "\n   [DEBUG] Other exception: " . get_class($e) . " - " . $e->getMessage() . "\n";
        exit(1);
    }

    echo "\n";

    // ========================================================================
    // 테스트 2: 본인 게시글 조회 (모든 visibility 포함)
    // Test 2: View own posts (all visibility included)
    // ========================================================================
    echo "🧪 테스트 2: 본인 게시글 조회 (모든 visibility 포함)\n";
    echo "🧪 Test 2: View own posts (all visibility included)\n";

    $result = get_user_posts(['user_id' => $userA->id]);

    if ($result instanceof PostListModel) {
        echo "   ✅ PostListModel 객체 반환 성공 / PostListModel object returned\n";
        echo "   - 조회된 게시글 수 / Posts count: " . count($result->posts) . "\n";

        // public, friends, private 모두 포함되어야 함
        $visibility_counts = ['public' => 0, 'friends' => 0, 'private' => 0];
        foreach ($result->posts as $post) {
            if (isset($visibility_counts[$post->visibility])) {
                $visibility_counts[$post->visibility]++;
            }
        }

        echo "   - public: {$visibility_counts['public']}개\n";
        echo "   - friends: {$visibility_counts['friends']}개\n";
        echo "   - private: {$visibility_counts['private']}개\n";

        if ($visibility_counts['public'] >= 2 && $visibility_counts['friends'] >= 2 && $visibility_counts['private'] >= 1) {
            echo "   ✅ 본인 게시글은 모든 visibility 포함됨\n";
            echo "   ✅ Own posts include all visibility levels\n";
        } else {
            echo "   ❌ 본인 게시글 조회 실패\n";
            echo "   ❌ Failed to retrieve own posts\n";
            exit(1);
        }
    } else {
        echo "   ❌ PostListModel 객체가 반환되지 않음\n";
        echo "   ❌ PostListModel object not returned\n";
        exit(1);
    }

    echo "\n";

    // ========================================================================
    // 테스트 준비: User B를 위한 새 세션 (로그아웃 시뮬레이션)
    // Test Setup: New session for User B (logout simulation)
    // ========================================================================
    echo "🔧 테스트 준비: 로그아웃 상태 시뮬레이션\n";
    echo "🔧 Test Setup: Simulating logged-out state\n";

    // 세션 쿠키 제거 (로그아웃 시뮬레이션)
    unset($_COOKIE[SESSION_ID]);

    echo "   ✅ 로그아웃 상태로 변경 (세션 쿠키 제거)\n";
    echo "   ✅ Changed to logged-out state (session cookie removed)\n\n";

    // ========================================================================
    // 테스트 3: 로그아웃 상태에서 타인 게시글 조회 (public만)
    // Test 3: View others' posts when logged out (public only)
    // ========================================================================
    echo "🧪 테스트 3: 로그아웃 상태에서 타인 게시글 조회 (public만)\n";
    echo "🧪 Test 3: View others' posts when logged out (public only)\n";

    $result = get_user_posts(['user_id' => $userA->id]);

    if ($result instanceof PostListModel) {
        echo "   ✅ PostListModel 객체 반환 성공 / PostListModel object returned\n";
        echo "   - 조회된 게시글 수 / Posts count: " . count($result->posts) . "\n";

        // public만 포함되어야 함
        $all_public = true;
        foreach ($result->posts as $post) {
            if ($post->visibility !== 'public') {
                $all_public = false;
                break;
            }
        }

        if ($all_public && count($result->posts) >= 2) {
            echo "   ✅ 로그아웃 상태에서는 public 게시글만 조회됨\n";
            echo "   ✅ Only public posts visible when logged out\n";
        } else {
            echo "   ❌ 로그아웃 상태 테스트 실패\n";
            echo "   ❌ Logged-out state test failed\n";
            echo "   - all_public: " . ($all_public ? 'true' : 'false') . "\n";
            echo "   - posts count: " . count($result->posts) . "\n";
            exit(1);
        }
    } else {
        echo "   ❌ PostListModel 객체가 반환되지 않음\n";
        echo "   ❌ PostListModel object not returned\n";
        exit(1);
    }

    echo "\n";

    // ========================================================================
    // 테스트 준비: User B (apple) 생성 및 친구 관계 설정
    // Test Setup: Create User B (apple) and establish friendship
    // ========================================================================
    echo "🔧 테스트 준비: User B 로그인 및 친구 관계 설정\n";
    echo "🔧 Test Setup: User B login and friendship establishment\n";

    // User B (apple) 로그인을 위해 새 PHP 프로세스로 시뮬레이션
    // (static 캐시 문제로 인해 동일 프로세스에서 재로그인 불가)
    // 대신 데이터베이스에서 직접 User B 조회
    $userB = get_user_by_firebase_uid('apple');
    if (!$userB) {
        echo "   ⚠️ User B (apple)가 존재하지 않음. 테스트 스킵.\n";
        echo "   ⚠️ User B (apple) does not exist. Skipping test.\n";
    } else {
        echo "   User B 정보 / User B info: {$userB['first_name']} {$userB['last_name']} (ID: {$userB['id']})\n";

        // User A와 User B 친구 관계 설정
        // ⚠️ 주의: 실제로는 request_friend, accept_friend 함수를 사용해야 하지만,
        // 여기서는 테스트 목적으로 직접 DB에 삽입
        $pdo = pdo();
        $now = time();

        // 기존 친구 관계 확인
        $stmt = $pdo->prepare("SELECT * FROM friendships WHERE (user_id_1 = ? AND user_id_2 = ?) OR (user_id_1 = ? AND user_id_2 = ?)");
        $stmt->execute([$userA->id, $userB['id'], $userB['id'], $userA->id]);
        $existing_friendship = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$existing_friendship) {
            // 친구 관계 삽입
            $stmt = $pdo->prepare("INSERT INTO friendships (user_id_1, user_id_2, status, created_at, updated_at) VALUES (?, ?, 'accepted', ?, ?)");
            $stmt->execute([$userA->id, $userB['id'], $now, $now]);
            echo "   ✅ User A와 User B 친구 관계 설정 완료\n";
            echo "   ✅ Friendship established between User A and User B\n";
        } else {
            echo "   ✅ User A와 User B 이미 친구 관계\n";
            echo "   ✅ User A and User B are already friends\n";
        }
    }

    echo "\n";

    // ========================================================================
    // 테스트 4: 로그인 상태 + 친구 관계 (public + friends)
    // Test 4: Logged in + friends (public + friends)
    // ========================================================================
    echo "🧪 테스트 4: 로그인 상태 + 친구 관계 (public + friends)\n";
    echo "🧪 Test 4: Logged in + friends (public + friends)\n";

    if ($userB) {
        // User B로 로그인 시뮬레이션 (세션 쿠키 설정)
        $_COOKIE[SESSION_ID] = generate_session_id($userB);
        echo "   User B 로그인 시뮬레이션 / User B login simulated\n";

        $result = get_user_posts(['user_id' => $userA->id]);

        if ($result instanceof PostListModel) {
            echo "   ✅ PostListModel 객체 반환 성공 / PostListModel object returned\n";
            echo "   - 조회된 게시글 수 / Posts count: " . count($result->posts) . "\n";

            // public + friends 포함되어야 함 (private 제외)
            $visibility_counts = ['public' => 0, 'friends' => 0, 'private' => 0];
            foreach ($result->posts as $post) {
                if (isset($visibility_counts[$post->visibility])) {
                    $visibility_counts[$post->visibility]++;
                }
            }

            echo "   - public: {$visibility_counts['public']}개\n";
            echo "   - friends: {$visibility_counts['friends']}개\n";
            echo "   - private: {$visibility_counts['private']}개\n";

            if ($visibility_counts['public'] >= 2 && $visibility_counts['friends'] >= 2 && $visibility_counts['private'] === 0) {
                echo "   ✅ 친구 관계에서는 public + friends 게시글만 조회됨\n";
                echo "   ✅ Friends can see public + friends posts only\n";
            } else {
                echo "   ❌ 친구 관계 테스트 실패\n";
                echo "   ❌ Friendship test failed\n";
                exit(1);
            }
        } else {
            echo "   ❌ PostListModel 객체가 반환되지 않음\n";
            echo "   ❌ PostListModel object not returned\n";
            exit(1);
        }
    } else {
        echo "   ⚠️ User B가 없어 테스트 스킵\n";
        echo "   ⚠️ Test skipped (User B not found)\n";
    }

    echo "\n";

    // ========================================================================
    // 테스트 준비: User C (cherry) - 친구 아님
    // Test Setup: User C (cherry) - not friends
    // ========================================================================
    echo "🔧 테스트 준비: User C (친구 아님)\n";
    echo "🔧 Test Setup: User C (not friends)\n";

    $userC = get_user_by_firebase_uid('cherry');
    if (!$userC) {
        echo "   ⚠️ User C (cherry)가 존재하지 않음. 테스트 스킵.\n";
        echo "   ⚠️ User C (cherry) does not exist. Skipping test.\n";
    } else {
        echo "   User C 정보 / User C info: {$userC['first_name']} {$userC['last_name']} (ID: {$userC['id']})\n";
    }

    echo "\n";

    // ========================================================================
    // 테스트 5: 로그인 상태 + 친구 아님 (public만)
    // Test 5: Logged in + not friends (public only)
    // ========================================================================
    echo "🧪 테스트 5: 로그인 상태 + 친구 아님 (public만)\n";
    echo "🧪 Test 5: Logged in + not friends (public only)\n";

    if ($userC) {
        // User C로 로그인 시뮬레이션
        $_COOKIE[SESSION_ID] = generate_session_id($userC);
        echo "   User C 로그인 시뮬레이션 / User C login simulated\n";

        $result = get_user_posts(['user_id' => $userA->id]);

        if ($result instanceof PostListModel) {
            echo "   ✅ PostListModel 객체 반환 성공 / PostListModel object returned\n";
            echo "   - 조회된 게시글 수 / Posts count: " . count($result->posts) . "\n";

            // public만 포함되어야 함
            $all_public = true;
            foreach ($result->posts as $post) {
                if ($post->visibility !== 'public') {
                    $all_public = false;
                    break;
                }
            }

            if ($all_public && count($result->posts) >= 2) {
                echo "   ✅ 친구가 아닌 경우 public 게시글만 조회됨\n";
                echo "   ✅ Non-friends can see public posts only\n";
            } else {
                echo "   ❌ 친구 아님 테스트 실패\n";
                echo "   ❌ Non-friends test failed\n";
                exit(1);
            }
        } else {
            echo "   ❌ PostListModel 객체가 반환되지 않음\n";
            echo "   ❌ PostListModel object not returned\n";
            exit(1);
        }
    } else {
        echo "   ⚠️ User C가 없어 테스트 스킵\n";
        echo "   ⚠️ Test skipped (User C not found)\n";
    }

    echo "\n";

    // ========================================================================
    // 테스트 6: 차단 관계일 때 빈 목록 반환
    // Test 6: Return empty list when blocked
    // ========================================================================
    echo "🧪 테스트 6: 차단 관계일 때 빈 목록 반환\n";
    echo "🧪 Test 6: Return empty list when blocked\n";

    if ($userC) {
        // User A가 User C를 차단 (DB에 직접 삽입)
        $pdo = pdo();
        $now = time();

        // 기존 차단 관계 확인
        $stmt = $pdo->prepare("SELECT * FROM blocks WHERE (blocker_id = ? AND blocked_id = ?) OR (blocker_id = ? AND blocked_id = ?)");
        $stmt->execute([$userA->id, $userC['id'], $userC['id'], $userA->id]);
        $existing_block = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$existing_block) {
            $stmt = $pdo->prepare("INSERT INTO blocks (blocker_id, blocked_id, created_at) VALUES (?, ?, ?)");
            $stmt->execute([$userA->id, $userC['id'], $now]);
            echo "   ✅ User A가 User C를 차단 / User A blocked User C\n";
        } else {
            echo "   ✅ User A와 User C 이미 차단 관계\n";
            echo "   ✅ User A and User C already have a block relationship\n";
        }

        // User C로 로그인 상태에서 User A의 게시글 조회 시도
        $_COOKIE[SESSION_ID] = generate_session_id($userC);

        $result = get_user_posts(['user_id' => $userA->id]);

        if ($result instanceof PostListModel) {
            echo "   ✅ PostListModel 객체 반환 성공 / PostListModel object returned\n";
            echo "   - 조회된 게시글 수 / Posts count: " . count($result->posts) . "\n";

            if (count($result->posts) === 0) {
                echo "   ✅ 차단 관계에서는 빈 목록 반환됨\n";
                echo "   ✅ Empty list returned when blocked\n";
            } else {
                echo "   ❌ 차단 관계 테스트 실패 (게시글이 조회됨)\n";
                echo "   ❌ Block relationship test failed (posts visible)\n";
                exit(1);
            }

            // 차단 관계 삭제 (다른 테스트에 영향 주지 않도록)
            $stmt = $pdo->prepare("DELETE FROM blocks WHERE (blocker_id = ? AND blocked_id = ?) OR (blocker_id = ? AND blocked_id = ?)");
            $stmt->execute([$userA->id, $userC['id'], $userC['id'], $userA->id]);
            echo "   🧹 차단 관계 삭제 (테스트 정리)\n";
            echo "   🧹 Block relationship removed (test cleanup)\n";
        } else {
            echo "   ❌ PostListModel 객체가 반환되지 않음\n";
            echo "   ❌ PostListModel object not returned\n";
            exit(1);
        }
    } else {
        echo "   ⚠️ User C가 없어 테스트 스킵\n";
        echo "   ⚠️ Test skipped (User C not found)\n";
    }

    echo "\n";

    // ========================================================================
    // 테스트 완료: 생성된 테스트 게시글 삭제
    // Test Cleanup: Delete created test posts
    // ========================================================================
    echo "🧹 테스트 완료: 생성된 테스트 게시글 삭제\n";
    echo "🧹 Test Cleanup: Deleting created test posts\n";

    // User A로 다시 로그인
    $_COOKIE[SESSION_ID] = generate_session_id(['id' => $userA->id, 'firebase_uid' => 'banana', 'phone_number' => $userA->phone_number]);

    foreach ($userA_posts as $post_id) {
        try {
            delete_post(['id' => $post_id]);
            echo "   게시글 삭제 / Post deleted: ID=$post_id\n";
        } catch (Throwable $e) {
            echo "   ⚠️  게시글 삭제 실패 / Post deletion failed: ID=$post_id\n";
        }
    }

    // 친구 관계 삭제 (테스트 정리)
    if ($userB) {
        $pdo = pdo();
        $stmt = $pdo->prepare("DELETE FROM friendships WHERE (user_id_1 = ? AND user_id_2 = ?) OR (user_id_1 = ? AND user_id_2 = ?)");
        $stmt->execute([$userA->id, $userB['id'], $userB['id'], $userA->id]);
        echo "   🧹 친구 관계 삭제 (테스트 정리)\n";
        echo "   🧹 Friendship removed (test cleanup)\n";
    }

    echo "\n======================================================================\n";
    echo "🎉 모든 get_user_posts() 함수 테스트 통과!\n";
    echo "🎉 All get_user_posts() function tests passed!\n";

} catch (Throwable $e) {
    echo "\n❌ 예외 발생 / Exception occurred: " . $e->getMessage() . "\n";
    echo "   파일 / File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "   스택 트레이스 / Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
