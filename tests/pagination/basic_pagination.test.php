<?php
/**
 * basic_pagination() 함수 테스트
 *
 * 실행 방법: php tests/pagination/basic_pagination.test.php
 */

// init.php 로드
require_once __DIR__ . '/../../init.php';

// 테스트 결과 저장
$test_results = [];
$test_count = 0;
$success_count = 0;

/**
 * 테스트 헬퍼 함수: 출력 캡처 및 검증
 */
function test_pagination_output(string $test_name, callable $test_fn, array $expected_contains): void
{
    global $test_results, $test_count, $success_count;

    $test_count++;
    echo "\n테스트 {$test_count}: {$test_name}\n";
    echo str_repeat("-", 60) . "\n";

    try {
        // 출력 버퍼 시작
        ob_start();
        $test_fn();
        $output = ob_get_clean();

        // 출력 내용 확인
        $all_found = true;
        $missing = [];

        foreach ($expected_contains as $expected) {
            if (strpos($output, $expected) === false) {
                $all_found = false;
                $missing[] = $expected;
            }
        }

        if ($all_found) {
            echo "✅ 성공\n";
            $success_count++;
            $test_results[] = ['name' => $test_name, 'status' => 'success'];
        } else {
            echo "❌ 실패\n";
            echo "찾지 못한 내용:\n";
            foreach ($missing as $miss) {
                echo "  - " . htmlspecialchars($miss) . "\n";
            }
            $test_results[] = ['name' => $test_name, 'status' => 'failed', 'missing' => $missing];
        }

        // 생성된 HTML 일부 출력 (디버깅용)
        $preview = substr($output, 0, 500);
        echo "\n생성된 HTML (일부):\n";
        echo htmlspecialchars($preview) . "\n";
    } catch (Exception $e) {
        echo "❌ 에러: " . $e->getMessage() . "\n";
        $test_results[] = ['name' => $test_name, 'status' => 'error', 'message' => $e->getMessage()];
    }

    echo "\n";
}

/**
 * 테스트 1: 기본 페이지네이션 (5페이지 표시)
 */
test_pagination_output(
    '기본 페이지네이션 - 5페이지 표시',
    function () {
        $_GET = ['gender' => 'M', 'name' => '김'];
        basic_pagination(3, 10, 5);
    },
    [
        'bi-chevron-double-left',  // << 버튼
        'bi-chevron-left',         // < 버튼
        'bi-chevron-right',        // > 버튼
        'bi-chevron-double-right', // >> 버튼
        'page-item active',        // 활성 페이지
        'gender=M',                // 쿼리 파라미터 유지
        'name=%EA%B9%80'           // 한글 인코딩 확인 (김)
    ]
);

/**
 * 테스트 2: 7페이지 표시
 */
test_pagination_output(
    '7페이지 표시',
    function () {
        $_GET = ['category' => 'books', 'sort' => 'recent'];
        basic_pagination(5, 20, 7);
    },
    [
        'category=books',
        'sort=recent',
        'page=1',  // 첫 페이지 링크
        'page=9'   // 다음 블록 첫 페이지 (8 + 1 = 9, 표시범위 2~8)
    ]
);

/**
 * 테스트 3: 9페이지 표시
 */
test_pagination_output(
    '9페이지 표시',
    function () {
        $_GET = [];
        basic_pagination(10, 50, 9);
    },
    [
        'page=1',   // 맨 처음
        'page=5',   // 이전 블록 마지막
        'page=14',  // 다음 블록 첫 페이지
        'page=50'   // 맨 끝
    ]
);

/**
 * 테스트 4: 첫 페이지 (이전 버튼 비활성화)
 */
test_pagination_output(
    '첫 페이지 - 이전 버튼 비활성화',
    function () {
        $_GET = [];
        basic_pagination(1, 10, 5);
    },
    [
        'page-item disabled',      // 비활성화된 버튼
        'page-item active'         // 활성 페이지 (1번)
    ]
);

/**
 * 테스트 5: 마지막 페이지 (다음 버튼 비활성화)
 */
test_pagination_output(
    '마지막 페이지 - 다음 버튼 비활성화',
    function () {
        $_GET = [];
        basic_pagination(10, 10, 5);
    },
    [
        'page-item disabled',      // 비활성화된 버튼
        'page-item active'         // 활성 페이지 (10번)
    ]
);

/**
 * 테스트 6: 페이지가 1개만 있을 때 (페이지네이션 미표시)
 */
test_pagination_output(
    '페이지 1개 - 페이지네이션 미표시',
    function () {
        $_GET = [];
        basic_pagination(1, 1, 5);
    },
    []  // 아무것도 출력되지 않아야 함
);

/**
 * 테스트 7: 짝수 display_pages 입력 (자동 홀수 변환)
 */
test_pagination_output(
    '짝수 입력 - 자동 홀수 변환 (6 -> 7)',
    function () {
        $_GET = [];
        basic_pagination(5, 20, 6);  // 6을 입력하면 자동으로 7로 변환
    },
    [
        'page=2',   // 5 - 3 = 2
        'page=8'    // 5 + 3 = 8 (7개 표시)
    ]
);

/**
 * 테스트 8: $query_params 직접 지정
 */
test_pagination_output(
    '$query_params 직접 지정',
    function () {
        $params = [
            'gender' => 'F',
            'age_start' => 25,
            'age_end' => 35,
            'search' => '테스트'
        ];
        basic_pagination(2, 10, 5, $params);
    },
    [
        'gender=F',
        'age_start=25',
        'age_end=35',
        'search=%ED%85%8C%EC%8A%A4%ED%8A%B8',  // '테스트' URL 인코딩
        'page=1',
        'page=3'
    ]
);

/**
 * 테스트 9: 블록 이동 로직 검증 - 중간 페이지
 */
test_pagination_output(
    '블록 이동 로직 - 중간 페이지',
    function () {
        $_GET = [];
        // 페이지 15, 총 30페이지, 7개 표시
        // 표시 범위: 12 ~ 18
        // 이전 블록 마지막: 11
        // 다음 블록 첫 페이지: 19
        basic_pagination(15, 30, 7);
    },
    [
        'page=1',   // 맨 처음
        'page=11',  // 이전 블록 마지막
        'page=19',  // 다음 블록 첫 페이지
        'page=30'   // 맨 끝
    ]
);

/**
 * 테스트 10: 총 페이지가 display_pages보다 작을 때
 */
test_pagination_output(
    '총 페이지 < display_pages',
    function () {
        $_GET = [];
        // 총 3페이지만 있는데 7개 표시 옵션
        basic_pagination(2, 3, 7);
    },
    [
        'page=1',
        'page=3',
        'page-item active'  // 현재 페이지(2번)는 활성 상태로 span 태그로 표시됨
    ]
);

// 테스트 결과 요약
echo "\n" . str_repeat("=", 60) . "\n";
echo "테스트 결과 요약\n";
echo str_repeat("=", 60) . "\n";

foreach ($test_results as $result) {
    $status_icon = $result['status'] === 'success' ? '✅' : '❌';
    echo "{$status_icon} {$result['name']} - {$result['status']}\n";

    if (isset($result['missing'])) {
        echo "   찾지 못한 내용:\n";
        foreach ($result['missing'] as $miss) {
            echo "     - {$miss}\n";
        }
    }

    if (isset($result['message'])) {
        echo "   에러 메시지: {$result['message']}\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "총 테스트: {$test_count}개\n";
echo "✅ 성공: {$success_count}개\n";
echo "❌ 실패: " . ($test_count - $success_count) . "개\n";
echo "📈 성공률: " . round(($success_count / $test_count) * 100, 1) . "%\n";
echo str_repeat("=", 60) . "\n";

// 모든 테스트가 성공하면 0, 하나라도 실패하면 1 반환
exit($success_count === $test_count ? 0 : 1);
