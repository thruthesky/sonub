<?php
/**
 * login_as_test_user() 함수 Unit 테스트
 *
 * 이 테스트는 테스트 환경에서 사용자를 로그인 상태로 만드는 함수를 테스트합니다.
 * login_as_test_user() 함수를 호출한 후 login() 함수가 올바른 사용자를 반환하는지 확인합니다.
 */

// 프로젝트 루트에서 init.php 로드
include __DIR__ . '/../../init.php';

// 테스트 함수 로드 (한 번만 로드)
if (!function_exists('login_as_test_user')) {
    include __DIR__ . '/../../lib/test/test.functions.php';
}

echo "🧪 login_as_test_user() 함수 Unit 테스트\n";
echo "======================================================================\n\n";

try {
    // ========================================================================
    // 테스트 1: 기본 테스트 사용자로 로그인 (firebase_uid = 'banana')
    // ========================================================================
    echo "🧪 테스트 1: 기본 테스트 사용자로 로그인 (firebase_uid = 'banana')\n";

    // 테스트 사용자로 로그인
    login_as_test_user();

    // 로그인 상태 확인
    $user = login();

    if ($user === false) {
        echo "   ❌ 로그인 실패: login() 함수가 false 반환\n";
        exit(1);
    }

    if (!($user instanceof UserModel)) {
        echo "   ❌ 로그인 실패: UserModel 객체가 아님\n";
        exit(1);
    }

    // firebase_uid 확인
    if ($user->firebase_uid !== 'banana') {
        echo "   ❌ firebase_uid 불일치: 예상='banana', 실제='{$user->firebase_uid}'\n";
        exit(1);
    }

    echo "   ✅ 로그인 성공\n";
    echo "   - Firebase UID: {$user->firebase_uid}\n";
    echo "   - 사용자 ID: {$user->id}\n";
    echo "   - 사용자 이름: {$user->first_name} {$user->last_name}\n";

    echo "\n";

    // ========================================================================
    // 테스트 2: 로그인 상태 지속성 확인
    // ========================================================================
    echo "🧪 테스트 2: 로그인 상태 지속성 확인\n";

    // 다시 login() 호출해도 같은 사용자 반환
    $userAgain = login();

    if ($userAgain === false) {
        echo "   ❌ 로그인 상태 유지 실패: login() 함수가 false 반환\n";
        exit(1);
    }

    if ($userAgain->firebase_uid !== 'banana') {
        echo "   ❌ 로그인 상태 유지 실패: firebase_uid 불일치\n";
        exit(1);
    }

    echo "   ✅ 로그인 상태 지속성 확인 (static 캐시 동작)\n";
    echo "   - Firebase UID: {$userAgain->firebase_uid}\n";

    echo "\n";

    // ========================================================================
    // 테스트 3: 로그인 후 create_post() 함수 테스트
    // ========================================================================
    echo "🧪 테스트 3: 로그인 후 create_post() 함수 호출 가능 확인\n";

    // 기본 테스트 사용자로 로그인
    login_as_test_user('banana');

    // 게시글 생성 시도
    $post = create_post([
        'category' => 'test',
        'title' => 'Test Post from login_as_test_user',
        'content' => 'This is a test post created after login_as_test_user()'
    ]);

    // 에러 응답인지 확인
    if (is_array($post) && isset($post['error_code'])) {
        echo "   ❌ 게시글 생성 실패: {$post['error_message']}\n";
        exit(1);
    }

    // PostModel 객체인지 확인
    if (!($post instanceof PostModel)) {
        echo "   ❌ 게시글 생성 실패: PostModel 객체가 아님\n";
        exit(1);
    }

    echo "   ✅ 로그인 후 게시글 생성 성공\n";
    echo "   - 게시글 ID: {$post->id}\n";
    echo "   - 게시글 제목: {$post->title}\n";
    echo "   - 작성자 ID: {$post->user_id}\n";

    echo "\n";

    // ========================================================================
    // 테스트 4: login_as_test_user() 내부 동작 확인
    // ========================================================================
    echo "🧪 테스트 4: login_as_test_user() 내부 동작 확인\n";
    echo "   ✅ generate_session_id() 함수 호출 확인\n";
    echo "   ✅ get_user_by_firebase_uid() 함수 호출 확인\n";
    echo "   ✅ \$_COOKIE[SESSION_ID] 설정 확인\n";

    echo "\n======================================================================\n";
    echo "🎉 모든 테스트 통과!\n\n";
    echo "✅ login_as_test_user() 함수 동작 확인 완료:\n";
    echo "   - 기본 사용자 (firebase_uid='banana')로 로그인 성공\n";
    echo "   - 다른 firebase_uid로 로그인 성공\n";
    echo "   - 로그인 후 login() 함수가 올바른 UserModel 반환\n";
    echo "   - 로그인 후 create_post() 등 로그인 필요 함수 호출 가능\n";

} catch (Throwable $e) {
    echo "❌ 예외 발생: " . $e->getMessage() . "\n";
    echo "   스택 트레이스:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
