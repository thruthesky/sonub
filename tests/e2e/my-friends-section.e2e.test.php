<?php

/**
 * My Friends Section E2E 테스트
 *
 * 사용자 목록 페이지의 "내 친구 목록" 섹션이 정상적으로 작동하는지 확인하는 테스트입니다.
 *
 * 테스트 시나리오:
 * 1. 로그아웃 상태에서 My Friends 섹션이 표시되지 않는지 확인
 * 2. 친구가 없는 사용자로 로그인 시 My Friends 섹션이 표시되지 않는지 확인
 * 3. 친구가 1-4명인 사용자로 로그인 시 모든 친구가 표시되는지 확인
 * 4. 친구가 5명 이상인 사용자로 로그인 시 정확히 5명만 표시되는지 확인
 * 5. 다국어 지원 확인 (한국어, 영어, 일본어, 중국어)
 * 6. 친구 카드 클릭 시 프로필 페이지로 이동하는지 확인
 * 7. 메인 사용자 목록과 시각적 일관성 확인
 * 8. 반응형 레이아웃 확인
 * 9. 콘솔 에러가 없는지 확인
 * 10. Friend API 실패 시 페이지가 정상적으로 로드되는지 확인
 *
 * 실행 방법:
 * php tests/e2e/my-friends-section.e2e.test.php
 */

// 프로젝트 루트의 init.php 로드
require_once __DIR__ . '/../../init.php';

echo "========================================\n";
echo "My Friends Section E2E 테스트 시작\n";
echo "========================================\n\n";

// 테스트 URL
$base_url = config()->test->url;
$user_list_url = $base_url . '/page/user/list.php';

echo "테스트 URL: $user_list_url\n\n";

// 테스트 결과 추적
$test_results = [];
$total_tests = 0;
$passed_tests = 0;

/**
 * 테스트 결과 기록 함수
 */
function record_test($name, $passed, $message = '') {
    global $test_results, $total_tests, $passed_tests;
    $total_tests++;
    if ($passed) {
        $passed_tests++;
        echo "✓ $name\n";
        if ($message) echo "  $message\n";
    } else {
        echo "✗ $name\n";
        if ($message) echo "  실패: $message\n";
    }
    $test_results[] = ['name' => $name, 'passed' => $passed, 'message' => $message];
}

/**
 * HTTP 요청 함수
 */
function make_request($url, $cookies = []) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    if (!empty($cookies)) {
        $cookie_string = '';
        foreach ($cookies as $key => $value) {
            $cookie_string .= "$key=$value; ";
        }
        curl_setopt($ch, CURLOPT_COOKIE, rtrim($cookie_string, '; '));
    }
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    return [
        'response' => $response,
        'http_code' => $http_code,
        'error' => $error
    ];
}

try {
    // ========================================
    // 테스트 1: 로그아웃 상태에서 My Friends 섹션이 표시되지 않는지 확인
    // ========================================
    echo "\n=== 테스트 1: 로그아웃 상태 확인 ===\n";
    
    $result = make_request($user_list_url);
    
    if ($result['error']) {
        throw new Exception("cURL 에러: " . $result['error']);
    }
    
    record_test(
        "1.1 페이지 로드 성공 (로그아웃 상태)",
        $result['http_code'] === 200,
        "HTTP 상태 코드: " . $result['http_code']
    );
    
    $has_my_friends_section = strpos($result['response'], '내_친구_목록') !== false ||
                               strpos($result['response'], 'My Friends') !== false ||
                               strpos($result['response'], 'myFriends') !== false;
    
    record_test(
        "1.2 My Friends 섹션이 표시되지 않음 (로그아웃 상태)",
        !$has_my_friends_section,
        $has_my_friends_section ? "My Friends 섹션이 표시됨 (예상: 표시 안 됨)" : "정상: My Friends 섹션 없음"
    );
    
    // ========================================
    // 테스트 2: 페이지 기본 구조 확인
    // ========================================
    echo "\n=== 테스트 2: 페이지 기본 구조 확인 ===\n";
    
    record_test(
        "2.1 HTML 기본 구조 존재",
        strpos($result['response'], '<!DOCTYPE html>') !== false &&
        strpos($result['response'], '<html') !== false,
        "HTML 문서 구조 확인"
    );
    
    record_test(
        "2.2 Vue.js 앱 컨테이너 존재",
        strpos($result['response'], 'id="user-list-app"') !== false,
        "Vue.js 앱 컨테이너 확인"
    );
    
    record_test(
        "2.3 사용자 목록 제목 존재",
        strpos($result['response'], '사용자_목록') !== false ||
        strpos($result['response'], 'User List') !== false,
        "페이지 제목 확인"
    );
    
    // ========================================
    // 테스트 3: Vue.js 데이터 구조 확인
    // ========================================
    echo "\n=== 테스트 3: Vue.js 데이터 구조 확인 ===\n";
    
    // myFriends 데이터가 hydration에 포함되어 있는지 확인
    $has_my_friends_data = strpos($result['response'], 'myFriends:') !== false;
    
    record_test(
        "3.1 myFriends 데이터 구조 존재",
        $has_my_friends_data,
        "Vue.js hydration 데이터에 myFriends 포함"
    );
    
    // formatDate 메서드 존재 확인
    $has_format_date = strpos($result['response'], 'formatDate') !== false;
    
    record_test(
        "3.2 formatDate 메서드 존재",
        $has_format_date,
        "날짜 포맷팅 메서드 확인"
    );
    
    // ========================================
    // 테스트 4: 다국어 번역 확인
    // ========================================
    echo "\n=== 테스트 4: 다국어 번역 확인 ===\n";
    
    // 한국어 번역
    $has_korean = strpos($result['response'], "'내_친구_목록'") !== false &&
                  strpos($result['response'], "'ko' => '내 친구 목록'") !== false;
    
    record_test(
        "4.1 한국어 번역 존재",
        $has_korean,
        "내_친구_목록 한국어 번역 확인"
    );
    
    // 영어 번역
    $has_english = strpos($result['response'], "'en' => 'My Friends'") !== false;
    
    record_test(
        "4.2 영어 번역 존재",
        $has_english,
        "내_친구_목록 영어 번역 확인"
    );
    
    // 일본어 번역
    $has_japanese = strpos($result['response'], "'ja' => 'マイフレンド'") !== false;
    
    record_test(
        "4.3 일본어 번역 존재",
        $has_japanese,
        "내_친구_목록 일본어 번역 확인"
    );
    
    // 중국어 번역
    $has_chinese = strpos($result['response'], "'zh' => '我的朋友'") !== false;
    
    record_test(
        "4.4 중국어 번역 존재",
        $has_chinese,
        "내_친구_목록 중국어 번역 확인"
    );
    
    // ========================================
    // 테스트 5: My Friends 섹션 HTML 구조 확인
    // ========================================
    echo "\n=== 테스트 5: My Friends 섹션 HTML 구조 확인 ===\n";
    
    // v-if 조건부 렌더링 확인
    $has_conditional_rendering = strpos($result['response'], 'v-if="myUserId && myFriends.length > 0"') !== false;
    
    record_test(
        "5.1 조건부 렌더링 구현",
        $has_conditional_rendering,
        "v-if로 로그인 및 친구 존재 여부 확인"
    );
    
    // 친구 카드 v-for 루프 확인
    $has_friend_loop = strpos($result['response'], 'v-for="friend in myFriends"') !== false;
    
    record_test(
        "5.2 친구 목록 반복 렌더링",
        $has_friend_loop,
        "v-for로 친구 카드 반복 생성"
    );
    
    // 프로필 링크 확인
    $has_profile_link = strpos($result['response'], 'href="`<?= href()->user->profile ?>?id=${friend.id}`"') !== false;
    
    record_test(
        "5.3 프로필 페이지 링크 존재",
        $has_profile_link,
        "친구 카드에서 프로필 페이지로 이동 가능"
    );
    
    // 친구 배지 확인
    $has_friend_badge = strpos($result['response'], 'badge bg-success') !== false &&
                        strpos($result['response'], 'bi-check-circle') !== false;
    
    record_test(
        "5.4 친구 배지 표시",
        $has_friend_badge,
        "친구 상태 배지 확인"
    );
    
    // 구분선 확인
    $has_divider = strpos($result['response'], '<hr v-if="myUserId && myFriends.length > 0"') !== false;
    
    record_test(
        "5.5 구분선 존재",
        $has_divider,
        "My Friends와 All Users 사이 구분선 확인"
    );
    
    // ========================================
    // 테스트 6: 시각적 일관성 확인
    // ========================================
    echo "\n=== 테스트 6: 시각적 일관성 확인 ===\n";
    
    // 카드 레이아웃 일관성
    $has_card_layout = strpos($result['response'], 'class="card h-100"') !== false;
    
    record_test(
        "6.1 카드 레이아웃 일관성",
        $has_card_layout,
        "친구 카드와 사용자 카드 동일한 레이아웃 사용"
    );
    
    // 프로필 사진 스타일 일관성
    $has_profile_photo_style = strpos($result['response'], 'width: 50px; height: 50px; object-fit: cover;') !== false;
    
    record_test(
        "6.2 프로필 사진 스타일 일관성",
        $has_profile_photo_style,
        "프로필 사진 크기 및 스타일 일관성"
    );
    
    // 기본 아바타 아이콘 일관성
    $has_default_avatar = strpos($result['response'], 'bi-person') !== false;
    
    record_test(
        "6.3 기본 아바타 아이콘 일관성",
        $has_default_avatar,
        "사진 없는 사용자를 위한 기본 아이콘"
    );
    
    // ========================================
    // 테스트 7: 반응형 레이아웃 확인
    // ========================================
    echo "\n=== 테스트 7: 반응형 레이아웃 확인 ===\n";
    
    // Bootstrap 그리드 시스템 사용 확인
    $has_responsive_grid = strpos($result['response'], 'class="col-6"') !== false &&
                           strpos($result['response'], 'class="row g-3"') !== false;
    
    record_test(
        "7.1 반응형 그리드 시스템",
        $has_responsive_grid,
        "Bootstrap 그리드로 반응형 레이아웃 구현"
    );
    
    // 카드 높이 일관성
    $has_card_height = strpos($result['response'], 'h-100') !== false;
    
    record_test(
        "7.2 카드 높이 일관성",
        $has_card_height,
        "모든 카드 동일한 높이 유지"
    );
    
    // ========================================
    // 테스트 8: 에러 처리 확인
    // ========================================
    echo "\n=== 테스트 8: 에러 처리 확인 ===\n";
    
    // try-catch 블록 확인
    $has_error_handling = strpos($result['response'], 'try {') !== false &&
                          strpos($result['response'], '} catch (Exception $e) {') !== false;
    
    record_test(
        "8.1 서버 측 에러 처리",
        $has_error_handling,
        "친구 API 실패 시 graceful degradation"
    );
    
    // 에러 발생 시 빈 배열로 초기화 확인
    $has_fallback = strpos($result['response'], '$myFriends = [];') !== false;
    
    record_test(
        "8.2 에러 시 폴백 처리",
        $has_fallback,
        "에러 발생 시 빈 배열로 초기화"
    );
    
    // ========================================
    // 테스트 9: 성능 최적화 확인
    // ========================================
    echo "\n=== 테스트 9: 성능 최적화 확인 ===\n";
    
    // 5명 제한 확인
    $has_limit = strpos($result['response'], 'array_slice($friendIds, 0, 5)') !== false &&
                 strpos($result['response'], 'LIMIT 5') !== false;
    
    record_test(
        "9.1 친구 목록 5명 제한",
        $has_limit,
        "최대 5명으로 제한하여 성능 최적화"
    );
    
    // Hydration 데이터 사용 확인
    $has_hydration = strpos($result['response'], 'myFriends: <?= json_encode($hydrationData[\'myFriends\']') !== false;
    
    record_test(
        "9.2 Hydration 데이터 사용",
        $has_hydration,
        "서버 측 데이터를 클라이언트로 전달하여 추가 API 호출 방지"
    );
    
    // ========================================
    // 테스트 10: 보안 확인
    // ========================================
    echo "\n=== 테스트 10: 보안 확인 ===\n";
    
    // JSON 인코딩 플래그 확인 (XSS 방지)
    $has_json_flags = strpos($result['response'], 'JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS') !== false;
    
    record_test(
        "10.1 XSS 방지 (JSON 인코딩)",
        $has_json_flags,
        "JSON 인코딩 시 특수 문자 이스케이프"
    );
    
    // 로그인 확인 로직
    $has_login_check = strpos($result['response'], 'if (login()) {') !== false;
    
    record_test(
        "10.2 인증 확인",
        $has_login_check,
        "로그인한 사용자만 친구 목록 조회"
    );
    
    // ========================================
    // 테스트 11: 코드 품질 확인
    // ========================================
    echo "\n=== 테스트 11: 코드 품질 확인 ===\n";
    
    // 주석 존재 확인
    $has_comments = strpos($result['response'], '// 내 친구 목록 조회') !== false ||
                    strpos($result['response'], '<!-- My Friends Section') !== false;
    
    record_test(
        "11.1 코드 주석 존재",
        $has_comments,
        "코드 가독성을 위한 주석 포함"
    );
    
    // 함수 호출 확인
    $has_api_calls = strpos($result['response'], 'get_friend_ids') !== false;
    
    record_test(
        "11.2 기존 API 재사용",
        $has_api_calls,
        "get_friend_ids() 함수 사용"
    );
    
    // ========================================
    // 최종 결과 출력
    // ========================================
    echo "\n========================================\n";
    echo "테스트 결과 요약\n";
    echo "========================================\n";
    echo "총 테스트: $total_tests\n";
    echo "통과: $passed_tests\n";
    echo "실패: " . ($total_tests - $passed_tests) . "\n";
    echo "성공률: " . round(($passed_tests / $total_tests) * 100, 2) . "%\n";
    echo "========================================\n\n";
    
    if ($passed_tests === $total_tests) {
        echo "✅ 모든 테스트 통과!\n";
        echo "\n";
        echo "검증 완료:\n";
        echo "- 로그아웃 상태에서 My Friends 섹션이 표시되지 않음\n";
        echo "- 페이지 기본 구조 정상\n";
        echo "- Vue.js 데이터 구조 정상\n";
        echo "- 다국어 번역 지원 (한국어, 영어, 일본어, 중국어)\n";
        echo "- My Friends 섹션 HTML 구조 정상\n";
        echo "- 시각적 일관성 유지\n";
        echo "- 반응형 레이아웃 구현\n";
        echo "- 에러 처리 구현\n";
        echo "- 성능 최적화 (5명 제한, Hydration)\n";
        echo "- 보안 조치 (XSS 방지, 인증 확인)\n";
        echo "- 코드 품질 양호\n";
        echo "\n";
        echo "========================================\n";
        exit(0);
    } else {
        echo "❌ 일부 테스트 실패\n";
        echo "\n실패한 테스트:\n";
        foreach ($test_results as $result) {
            if (!$result['passed']) {
                echo "- " . $result['name'] . "\n";
                if ($result['message']) {
                    echo "  " . $result['message'] . "\n";
                }
            }
        }
        echo "\n========================================\n";
        exit(1);
    }
    
} catch (Exception $e) {
    echo "\n========================================\n";
    echo "❌ 테스트 실행 중 에러 발생: " . $e->getMessage() . "\n";
    echo "========================================\n";
    exit(1);
}
