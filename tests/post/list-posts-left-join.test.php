<?php

/**
 * list_posts() LEFT JOIN 테스트
 *
 * list_posts() 함수가 LEFT JOIN을 사용하여 사용자 정보를 올바르게 가져오는지 테스트합니다.
 *
 * 테스트 시나리오:
 * 1. 테스트 사용자 생성
 * 2. 테스트 게시글 생성
 * 3. list_posts() 함수 호출
 * 4. 게시글에 사용자 정보(first_name, photo_url, firebase_uid)가 포함되어 있는지 확인
 * 5. LEFT JOIN이 정상적으로 동작하는지 검증
 */

require_once __DIR__ . '/../../init.php';

echo "========================================\n";
echo "list_posts() LEFT JOIN 테스트 시작\n";
echo "========================================\n\n";

try {
    // ====================================================================
    // 1단계: 테스트 사용자 생성
    // ====================================================================
    echo "1단계: 테스트 사용자 생성 중...\n";

    $firebase_uid = 'test_left_join_' . time();
    $test_user = create_user_record([
        'firebase_uid' => $firebase_uid,
        'first_name' => '테스트',
        'last_name' => '사용자',
        'birthday' => strtotime('1990-01-01'),
        'gender' => 'M',
        'photo_url' => 'https://example.com/test-photo.jpg'
    ]);

    echo "✅ 테스트 사용자 생성 성공\n";
    echo "   - 사용자 ID: {$test_user['id']}\n";
    echo "   - 이름: {$test_user['first_name']} {$test_user['last_name']}\n";
    echo "   - Firebase UID: {$test_user['firebase_uid']}\n";
    echo "   - 프로필 사진: {$test_user['photo_url']}\n\n";

    // ====================================================================
    // 2단계: 로그인 처리 (테스트용 직접 세션 설정)
    // ====================================================================
    echo "2단계: 로그인 처리 중...\n";

    // 테스트 환경에서는 쿠키를 설정할 수 없으므로 $_COOKIE를 직접 설정
    // generate_session_id() 함수를 사용하여 세션 ID 생성
    $session_id = generate_session_id($test_user);
    $_COOKIE[SESSION_ID] = $session_id;
    echo "✅ 로그인 성공 (세션 ID: {$session_id})\n\n";

    echo "3단계: 테스트 게시글 생성 중...\n";

    $test_post = create_post([
        'category' => 'test-left-join',
        'title' => 'LEFT JOIN 테스트 게시글',
        'content' => 'LEFT JOIN이 정상적으로 동작하는지 테스트합니다.',
        'visibility' => 'public'
    ]);

    echo "✅ 테스트 게시글 생성 성공\n";
    echo "   - 게시글 ID: {$test_post->id}\n";
    echo "   - 제목: {$test_post->title}\n";
    echo "   - 작성자 ID: {$test_post->user_id}\n\n";

    // ====================================================================
    // 4단계: list_posts() 함수 호출 (LEFT JOIN 테스트)
    // ====================================================================
    echo "4단계: list_posts() 함수 호출 (LEFT JOIN 테스트)...\n";

    // 특정 카테고리의 게시글 목록 조회
    $post_list = list_posts([
        'category' => 'test-left-join',
        'limit' => 10
    ]);

    echo "✅ list_posts() 호출 성공\n";
    echo "   - 조회된 게시글 수: " . count($post_list->posts) . "\n\n";

    // ====================================================================
    // 5단계: LEFT JOIN 결과 검증
    // ====================================================================
    echo "5단계: LEFT JOIN 결과 검증 중...\n";

    // 게시글 목록이 비어있지 않은지 확인
    if (empty($post_list->posts)) {
        throw new Exception("❌ 테스트 실패: 게시글 목록이 비어있습니다.");
    }

    // 첫 번째 게시글 가져오기
    $first_post = $post_list->posts[0];

    echo "\n조회된 게시글 정보:\n";
    echo "   - 게시글 ID: {$first_post->id}\n";
    echo "   - 제목: {$first_post->title}\n";
    echo "   - 작성자 ID: {$first_post->user_id}\n";
    echo "   - 작성자 이름: {$first_post->first_name} (LEFT JOIN으로 가져온 값)\n";
    echo "   - 작성자 프로필 사진: {$first_post->photo_url} (LEFT JOIN으로 가져온 값)\n";
    echo "   - 작성자 Firebase UID: {$first_post->firebase_uid} (LEFT JOIN으로 가져온 값)\n\n";

    // ====================================================================
    // 6단계: 데이터 검증
    // ====================================================================
    echo "6단계: 데이터 검증 중...\n";

    // first_name이 존재하는지 확인
    if (empty($first_post->first_name)) {
        throw new Exception("❌ 테스트 실패: first_name이 비어있습니다. LEFT JOIN이 정상적으로 동작하지 않았습니다.");
    }

    // first_name이 테스트 사용자의 이름과 일치하는지 확인
    if ($first_post->first_name !== $test_user['first_name']) {
        throw new Exception("❌ 테스트 실패: first_name이 일치하지 않습니다. 예상: {$test_user['first_name']}, 실제: {$first_post->first_name}");
    }

    // photo_url이 존재하는지 확인
    if (empty($first_post->photo_url)) {
        throw new Exception("❌ 테스트 실패: photo_url이 비어있습니다. LEFT JOIN이 정상적으로 동작하지 않았습니다.");
    }

    // photo_url이 테스트 사용자의 프로필 사진과 일치하는지 확인
    if ($first_post->photo_url !== $test_user['photo_url']) {
        throw new Exception("❌ 테스트 실패: photo_url이 일치하지 않습니다. 예상: {$test_user['photo_url']}, 실제: {$first_post->photo_url}");
    }

    // firebase_uid가 존재하는지 확인
    if (empty($first_post->firebase_uid)) {
        throw new Exception("❌ 테스트 실패: firebase_uid가 비어있습니다. LEFT JOIN이 정상적으로 동작하지 않았습니다.");
    }

    // firebase_uid가 테스트 사용자의 Firebase UID와 일치하는지 확인
    if ($first_post->firebase_uid !== $test_user['firebase_uid']) {
        throw new Exception("❌ 테스트 실패: firebase_uid가 일치하지 않습니다. 예상: {$test_user['firebase_uid']}, 실제: {$first_post->firebase_uid}");
    }

    echo "✅ 모든 데이터 검증 성공\n";
    echo "   - first_name: {$first_post->first_name} ✅\n";
    echo "   - photo_url: {$first_post->photo_url} ✅\n";
    echo "   - firebase_uid: {$first_post->firebase_uid} ✅\n\n";

    // ====================================================================
    // 7단계: 테스트 데이터 정리
    // ====================================================================
    echo "7단계: 테스트 데이터 정리 중...\n";

    // 테스트 게시글 삭제
    delete_post(['id' => $test_post->id]);
    echo "✅ 테스트 게시글 삭제 완료\n";

    // 테스트 사용자 삭제 (PDO 직접 사용)
    $pdo = pdo();
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$test_user['id']]);
    echo "✅ 테스트 사용자 삭제 완료\n\n";

    // ====================================================================
    // 테스트 성공
    // ====================================================================
    echo "========================================\n";
    echo "✅ list_posts() LEFT JOIN 테스트 성공\n";
    echo "========================================\n";
    echo "\n테스트 결과:\n";
    echo "- LEFT JOIN이 정상적으로 동작합니다.\n";
    echo "- posts 테이블과 users 테이블이 올바르게 연결됩니다.\n";
    echo "- 게시글 목록에 사용자 정보(first_name, photo_url, firebase_uid)가 포함됩니다.\n";

} catch (Throwable $e) {
    // ====================================================================
    // 테스트 실패
    // ====================================================================
    echo "\n========================================\n";
    echo "❌ list_posts() LEFT JOIN 테스트 실패\n";
    echo "========================================\n";
    echo "에러 메시지: " . $e->getMessage() . "\n";
    echo "에러 파일: " . $e->getFile() . "\n";
    echo "에러 라인: " . $e->getLine() . "\n";

    // 테스트 데이터 정리 시도
    if (isset($test_post->id)) {
        try {
            delete_post(['id' => $test_post->id]);
            echo "\n테스트 게시글 삭제 완료\n";
        } catch (Throwable $cleanup_error) {
            echo "\n⚠️ 테스트 게시글 삭제 실패: " . $cleanup_error->getMessage() . "\n";
        }
    }

    if (isset($test_user['id'])) {
        try {
            $pdo = pdo();
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$test_user['id']]);
            echo "테스트 사용자 삭제 완료\n";
        } catch (Throwable $cleanup_error) {
            echo "⚠️ 테스트 사용자 삭제 실패: " . $cleanup_error->getMessage() . "\n";
        }
    }

    exit(1);
}
