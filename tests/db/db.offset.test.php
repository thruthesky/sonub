<?php
/**
 * db()->offset() 메서드 유닛 테스트
 *
 * 실행 방법: php tests/db/db.offset.test.php
 */

// init.php 로드 (프로젝트 루트 기준 상대 경로)
include __DIR__ . '/../../init.php';

echo "=== db()->offset() 메서드 테스트 시작 ===\n\n";

// 테스트용 임시 데이터 생성
echo "📋 테스트 데이터 준비 중...\n";
echo str_repeat('-', 50) . "\n";

try {
    // 기존 테스트 데이터 정리
    db()->delete()->from('users')->where('display_name LIKE ?', ['test_offset_%'])->execute();
    echo "✅ 기존 테스트 데이터 정리 완료\n";

    // 30개의 테스트 사용자 생성
    $userIds = [];
    for ($i = 1; $i <= 30; $i++) {
        $userId = db()->insert([
            'firebase_uid' => 'test_offset_' . $i,
            'first_name' => 'test_offset_' . $i,
            'last_name' => '',
            'middle_name' => '',
            'created_at' => time(),
            'updated_at' => time(),
        ])->into('users');
        $userIds[] = $userId;
    }
    echo "✅ 30개의 테스트 사용자 생성 완료 (ID: {$userIds[0]} ~ {$userIds[29]})\n\n";
} catch (Exception $e) {
    echo "❌ 테스트 데이터 준비 실패: " . $e->getMessage() . "\n\n";
    exit(1);
}

// ==========================================
// 테스트 1: 기본 offset() 사용
// ==========================================
echo "📋 테스트 1: 기본 offset() 사용 - 10개 건너뛰고 10개 조회\n";
echo str_repeat('-', 50) . "\n";

try {
    $results = db()->select('*')
        ->from('users')
        ->where('first_name LIKE ?', ['test_offset_%'])
        ->orderBy('id', 'ASC')
        ->limit(10)
        ->offset(10)
        ->get();

    if (count($results) === 10) {
        echo "✅ 성공: 10개 레코드 조회됨\n";
        echo "   첫 번째 레코드: {$results[0]['first_name']} (ID: {$results[0]['id']})\n";
        echo "   마지막 레코드: {$results[9]['first_name']} (ID: {$results[9]['id']})\n";

        // 11번째 사용자부터 조회되었는지 확인
        if ($results[0]['first_name'] === 'test_offset_11') {
            echo "✅ 올바른 offset 적용: 11번째 사용자부터 시작\n";
        } else {
            echo "❌ offset 적용 오류: {$results[0]['first_name']}부터 시작 (예상: test_offset_11)\n";
        }
    } else {
        echo "❌ 실패: " . count($results) . "개 레코드 조회됨 (예상: 10개)\n";
    }
} catch (Exception $e) {
    echo "❌ 예외 발생: " . $e->getMessage() . "\n";
}
echo "\n";

// ==========================================
// 테스트 2: offset(0) - 첫 페이지
// ==========================================
echo "📋 테스트 2: offset(0) - 첫 페이지 (0-9)\n";
echo str_repeat('-', 50) . "\n";

try {
    $results = db()->select('*')
        ->from('users')
        ->where('first_name LIKE ?', ['test_offset_%'])
        ->orderBy('id', 'ASC')
        ->limit(10)
        ->offset(0)
        ->get();

    if (count($results) === 10 && $results[0]['first_name'] === 'test_offset_1') {
        echo "✅ 성공: 첫 페이지 조회 (test_offset_1 ~ test_offset_10)\n";
        echo "   첫 번째: {$results[0]['first_name']}\n";
        echo "   마지막: {$results[9]['first_name']}\n";
    } else {
        echo "❌ 실패: 첫 번째 레코드 = {$results[0]['first_name']} (예상: test_offset_1)\n";
    }
} catch (Exception $e) {
    echo "❌ 예외 발생: " . $e->getMessage() . "\n";
}
echo "\n";

// ==========================================
// 테스트 3: 페이지네이션 - 3개 페이지 조회
// ==========================================
echo "📋 테스트 3: 페이지네이션 - 각 페이지 10개씩, 3페이지 조회\n";
echo str_repeat('-', 50) . "\n";

try {
    $perPage = 10;
    $success = true;

    for ($page = 1; $page <= 3; $page++) {
        $offset = ($page - 1) * $perPage;

        $results = db()->select('*')
            ->from('users')
            ->where('first_name LIKE ?', ['test_offset_%'])
            ->orderBy('id', 'ASC')
            ->limit($perPage)
            ->offset($offset)
            ->get();

        $expectedStart = (($page - 1) * $perPage) + 1;
        $expectedEnd = $page * $perPage;

        echo "   페이지 {$page}: ";
        if (count($results) === $perPage) {
            $actualStart = str_replace('test_offset_', '', $results[0]['first_name']);
            $actualEnd = str_replace('test_offset_', '', $results[9]['first_name']);

            if ($actualStart == $expectedStart && $actualEnd == $expectedEnd) {
                echo "✅ test_offset_{$expectedStart} ~ test_offset_{$expectedEnd}\n";
            } else {
                echo "❌ 범위 오류 (실제: {$actualStart} ~ {$actualEnd}, 예상: {$expectedStart} ~ {$expectedEnd})\n";
                $success = false;
            }
        } else {
            echo "❌ 레코드 수 오류 (" . count($results) . "개)\n";
            $success = false;
        }
    }

    if ($success) {
        echo "✅ 성공: 모든 페이지가 올바르게 조회됨\n";
    } else {
        echo "❌ 실패: 일부 페이지 조회 오류\n";
    }
} catch (Exception $e) {
    echo "❌ 예외 발생: " . $e->getMessage() . "\n";
}
echo "\n";

// ==========================================
// 테스트 4: limit() 없이 offset() 호출 (예외 발생 예상)
// ==========================================
echo "📋 테스트 4: limit() 없이 offset() 호출 (예외 발생 예상)\n";
echo str_repeat('-', 50) . "\n";

try {
    $results = db()->select('*')
        ->from('users')
        ->offset(10)  // limit() 없이 offset() 호출
        ->get();

    echo "❌ 실패: 예외가 발생해야 하는데 발생하지 않음\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'limit()을 호출해야') !== false) {
        echo "✅ 성공: 예상된 예외 발생\n";
        echo "   예외 메시지: {$e->getMessage()}\n";
    } else {
        echo "❌ 실패: 다른 예외 발생\n";
        echo "   예외 메시지: {$e->getMessage()}\n";
    }
}
echo "\n";

// ==========================================
// 테스트 5: 큰 offset 값 (20개 건너뛰고 10개 조회)
// ==========================================
echo "📋 테스트 5: 큰 offset 값 - 20개 건너뛰고 10개 조회\n";
echo str_repeat('-', 50) . "\n";

try {
    $results = db()->select('*')
        ->from('users')
        ->where('first_name LIKE ?', ['test_offset_%'])
        ->orderBy('id', 'ASC')
        ->limit(10)
        ->offset(20)
        ->get();

    if (count($results) === 10 && $results[0]['first_name'] === 'test_offset_21') {
        echo "✅ 성공: test_offset_21 ~ test_offset_30 조회됨\n";
        echo "   첫 번째: {$results[0]['first_name']}\n";
        echo "   마지막: {$results[9]['first_name']}\n";
    } else {
        echo "❌ 실패\n";
        echo "   레코드 수: " . count($results) . " (예상: 10)\n";
        if (!empty($results)) {
            echo "   첫 번째: {$results[0]['first_name']} (예상: test_offset_21)\n";
        }
    }
} catch (Exception $e) {
    echo "❌ 예외 발생: " . $e->getMessage() . "\n";
}
echo "\n";

// ==========================================
// 테스트 6: offset이 전체 레코드 수를 초과하는 경우
// ==========================================
echo "📋 테스트 6: offset이 전체 레코드 수를 초과 (빈 결과 예상)\n";
echo str_repeat('-', 50) . "\n";

try {
    $results = db()->select('*')
        ->from('users')
        ->where('first_name LIKE ?', ['test_offset_%'])
        ->orderBy('id', 'ASC')
        ->limit(10)
        ->offset(100)  // 30개만 있는데 100개 건너뜀
        ->get();

    if (count($results) === 0) {
        echo "✅ 성공: 빈 배열 반환됨 (레코드 0개)\n";
    } else {
        echo "❌ 실패: " . count($results) . "개 레코드 조회됨 (예상: 0개)\n";
    }
} catch (Exception $e) {
    echo "❌ 예외 발생: " . $e->getMessage() . "\n";
}
echo "\n";

// ==========================================
// 테스트 7: 페이지네이션 헬퍼 함수 테스트
// ==========================================
echo "📋 테스트 7: 페이지네이션 헬퍼 함수\n";
echo str_repeat('-', 50) . "\n";

/**
 * 페이지네이션 헬퍼 함수
 */
function getPaginatedTestUsers($page, $perPage = 10) {
    $offset = ($page - 1) * $perPage;
    return db()->select('*')
        ->from('users')
        ->where('first_name LIKE ?', ['test_offset_%'])
        ->orderBy('id', 'ASC')
        ->limit($perPage)
        ->offset($offset)
        ->get();
}

try {
    $page1 = getPaginatedTestUsers(1);  // 1-10
    $page2 = getPaginatedTestUsers(2);  // 11-20
    $page3 = getPaginatedTestUsers(3);  // 21-30

    $success = true;

    if ($page1[0]['first_name'] !== 'test_offset_1' || $page1[9]['first_name'] !== 'test_offset_10') {
        echo "❌ 페이지 1 오류\n";
        $success = false;
    }

    if ($page2[0]['first_name'] !== 'test_offset_11' || $page2[9]['first_name'] !== 'test_offset_20') {
        echo "❌ 페이지 2 오류\n";
        $success = false;
    }

    if ($page3[0]['first_name'] !== 'test_offset_21' || $page3[9]['first_name'] !== 'test_offset_30') {
        echo "❌ 페이지 3 오류\n";
        $success = false;
    }

    if ($success) {
        echo "✅ 성공: 페이지네이션 헬퍼 함수 정상 동작\n";
        echo "   페이지 1: {$page1[0]['first_name']} ~ {$page1[9]['first_name']}\n";
        echo "   페이지 2: {$page2[0]['first_name']} ~ {$page2[9]['first_name']}\n";
        echo "   페이지 3: {$page3[0]['first_name']} ~ {$page3[9]['first_name']}\n";
    }
} catch (Exception $e) {
    echo "❌ 예외 발생: " . $e->getMessage() . "\n";
}
echo "\n";

// ==========================================
// 테스트 완료 및 정리
// ==========================================
echo "=== 모든 테스트 완료 ===\n\n";

// 테스트 데이터 정리
echo "❓ 테스트 데이터를 삭제하시겠습니까? (y/n): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
$cleanup = trim(strtolower($line));
fclose($handle);

if ($cleanup === 'y' || $cleanup === 'yes') {
    try {
        $deleted = db()->delete()
            ->from('users')
            ->where('first_name LIKE ?', ['test_offset_%'])
            ->execute();
        echo "✅ 테스트 데이터 삭제 완료 ({$deleted}개 레코드 삭제)\n";
    } catch (Exception $e) {
        echo "❌ 테스트 데이터 삭제 실패: " . $e->getMessage() . "\n";
    }
} else {
    echo "ℹ️  테스트 데이터가 보존됩니다 (first_name LIKE 'test_offset_%')\n";
}

echo "\n테스트 종료.\n";
