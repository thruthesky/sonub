<?php

/**
 * E2E 테스트: 게시물 작성 폼의 textarea 확장 기능
 *
 * 테스트 목적:
 * - textarea를 클릭/입력하면 높이가 1em에서 6em으로 확장되는지 확인
 * - Vue.js의 expanded 클래스가 제대로 적용되는지 확인
 */

include __DIR__ . '/../../init.php';

echo "🧪 E2E 테스트: 게시물 작성 폼 textarea 확장 기능\n";
echo "=" . str_repeat("=", 70) . "\n\n";

// 테스트할 URL
$url = 'https://local.sonub.com/post/list';

// cURL 설정
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

// HTTP 요청 실행
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// 테스트 1: HTTP 상태 코드 확인
echo "🧪 테스트 1: HTTP 상태 코드 확인\n";
if ($httpCode === 200) {
    echo "   ✅ HTTP 200 OK\n\n";
} else {
    echo "   ❌ 실패: HTTP {$httpCode}\n\n";
    exit(1);
}

// 테스트 2: section#post-list-create 존재 확인
echo "🧪 테스트 2: section#post-list-create 존재 확인\n";
if (str_contains($response, 'id="post-list-create"')) {
    echo "   ✅ section#post-list-create 발견\n\n";
} else {
    echo "   ❌ 실패: section#post-list-create를 찾을 수 없음\n\n";
    exit(1);
}

// 테스트 3: :class 바인딩 확인
echo "🧪 테스트 3: Vue.js :class 바인딩 확인\n";
if (preg_match('/:class="\{[^}]*expanded[^}]*\}"/', $response)) {
    echo "   ✅ :class=\"{ 'expanded': expanded}\" 바인딩 발견\n\n";
} else {
    echo "   ⚠️  경고: :class 바인딩을 찾을 수 없음\n\n";
}

// 테스트 4: textarea에 이벤트 리스너 확인
echo "🧪 테스트 4: textarea 이벤트 리스너 확인\n";
$events_found = 0;
if (str_contains($response, '@focus="expand"')) {
    echo "   ✅ @focus=\"expand\" 발견\n";
    $events_found++;
}
if (str_contains($response, '@click="expand"')) {
    echo "   ✅ @click=\"expand\" 발견\n";
    $events_found++;
}
if (str_contains($response, '@input="expand"')) {
    echo "   ✅ @input=\"expand\" 발견\n";
    $events_found++;
}
if (str_contains($response, '@keydown="expand"')) {
    echo "   ✅ @keydown=\"expand\" 발견\n";
    $events_found++;
}

if ($events_found === 4) {
    echo "   ✅ 모든 이벤트 리스너 확인 완료\n\n";
} else {
    echo "   ⚠️  경고: 일부 이벤트 리스너가 누락됨 ({$events_found}/4)\n\n";
}

// 테스트 5: CSS 스타일 확인
echo "🧪 테스트 5: CSS 스타일 확인\n";
if (str_contains($response, '#post-list-create textarea.form-control')) {
    echo "   ✅ 기본 CSS 선택자 발견\n";
}
if (str_contains($response, '#post-list-create.expanded textarea.form-control')) {
    echo "   ✅ 확장 CSS 선택자 발견\n";
}
if (str_contains($response, 'height: 1em !important')) {
    echo "   ✅ 기본 높이 (1em) 스타일 발견\n";
}
if (str_contains($response, 'height: 6em !important')) {
    echo "   ✅ 확장 높이 (6em) 스타일 발견\n\n";
} else {
    echo "   ❌ 실패: 확장 높이 스타일을 찾을 수 없음\n\n";
}

// 테스트 6: Vue.js expand() 메서드 확인
echo "🧪 테스트 6: Vue.js expand() 메서드 확인\n";
if (preg_match('/expand\s*\(\s*\)\s*\{/', $response)) {
    echo "   ✅ expand() 메서드 발견\n";
    if (str_contains($response, 'this.expanded = true')) {
        echo "   ✅ expanded 상태 변경 로직 발견\n\n";
    }
} else {
    echo "   ❌ 실패: expand() 메서드를 찾을 수 없음\n\n";
}

// 테스트 7: Vue.js 마운트 확인
echo "🧪 테스트 7: Vue.js 마운트 확인\n";
if (str_contains($response, ".mount('#post-list-create')")) {
    echo "   ✅ Vue.js 마운트 코드 발견\n\n";
} else {
    echo "   ❌ 실패: Vue.js 마운트 코드를 찾을 수 없음\n\n";
}

echo "\n";
echo "=" . str_repeat("=", 70) . "\n";
echo "✅ E2E 테스트 완료\n\n";

echo "📝 수동 테스트 가이드:\n";
echo "1. 브라우저에서 {$url} 접속\n";
echo "2. 개발자 도구 콘솔 열기 (F12)\n";
echo "3. textarea 클릭 또는 입력\n";
echo "4. 콘솔에서 'expand() 호출됨' 메시지 확인\n";
echo "5. textarea 높이가 6em으로 확장되는지 확인\n";
echo "6. section 배경색이 회색(#f8f9fa)으로 변경되는지 확인\n";
echo "7. Elements 탭에서 <section id=\"post-list-create\" class=\"mb-4 expanded\"> 확인\n\n";
