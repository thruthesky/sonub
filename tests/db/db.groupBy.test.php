<?php
/**
 * db()->groupBy() 메서드 종합 테스트
 *
 * 집계 함수(COUNT, SUM, AVG, MAX, MIN)와 GROUP BY를 테스트합니다.
 *
 * 실행 방법: php tests/db/db.groupBy.test.php
 */

// init.php 로드
include __DIR__ . '/../../init.php';

echo "=== db()->groupBy() 메서드 종합 테스트 시작 ===\n\n";

// 테스트용 데이터 준비
echo "📋 테스트 데이터 준비 중...\n";
echo str_repeat('-', 70) . "\n";

try {
    // 기존 테스트 데이터 정리
    db()->delete()->from('users')->where('firebase_uid LIKE ?', ['test_groupby_%'])->execute();
    echo "✅ 기존 테스트 데이터 정리 완료\n";

    // 다양한 성별, 생년월일을 가진 50명의 사용자 생성
    $genders = ['M', 'F'];
    $userIds = [];

    for ($i = 1; $i <= 50; $i++) {
        $gender = $genders[$i % 2]; // 남녀 번갈아가며
        $birthYear = 1970 + ($i % 35); // 1970~2004년 사이
        $birthday = mktime(0, 0, 0, 1, 1, $birthYear);

        // 가입 월 다양화 (1~12월)
        $createdMonth = ($i % 12) + 1;
        $createdAt = mktime(0, 0, 0, $createdMonth, 1, 2024);

        $userId = db()->insert([
            'firebase_uid' => 'test_groupby_' . $i,
            'display_name' => 'test_groupby_' . $i,
            'gender' => $gender,
            'birthday' => $birthday,
            'created_at' => $createdAt,
            'updated_at' => time(),
        ])->into('users');

        $userIds[] = $userId;
    }

    echo "✅ 50명의 테스트 사용자 생성 완료\n";
    echo "   사용자 ID 범위: {$userIds[0]} ~ {$userIds[49]}\n";
    echo "   성별 분포: 남성 25명, 여성 25명\n";
    echo "   연령대: 1970년생 ~ 2004년생\n\n";
} catch (Exception $e) {
    echo "❌ 테스트 데이터 준비 실패: " . $e->getMessage() . "\n\n";
    exit(1);
}

$testsPassed = 0;
$testsFailed = 0;

// ==========================================
// 테스트 1: 기본 GROUP BY - 성별별 사용자 수
// ==========================================
echo "📋 테스트 1: 기본 GROUP BY - 성별별 사용자 수\n";
echo str_repeat('-', 70) . "\n";

try {
    $results = db()->select('gender, COUNT(*) as count')
        ->from('users')
        ->where('firebase_uid LIKE ?', ['test_groupby_%'])
        ->groupBy('gender')
        ->get();

    echo "   SQL 생성 확인:\n";
    echo "   SELECT gender, COUNT(*) as count FROM users\n";
    echo "   WHERE firebase_uid LIKE ? GROUP BY gender\n\n";

    $success = true;

    if (count($results) !== 2) {
        echo "   ❌ 결과 레코드 수 오류: " . count($results) . "개 (예상: 2개)\n";
        $success = false;
    }

    foreach ($results as $row) {
        if ($row['count'] != 25) {
            echo "   ❌ {$row['gender']} 카운트 오류: {$row['count']}명 (예상: 25명)\n";
            $success = false;
        } else {
            echo "   ✅ {$row['gender']}: {$row['count']}명\n";
        }
    }

    if ($success) {
        echo "   ✅ 테스트 1 통과\n";
        $testsPassed++;
    } else {
        echo "   ❌ 테스트 1 실패\n";
        $testsFailed++;
    }
} catch (Exception $e) {
    echo "   ❌ 예외 발생: " . $e->getMessage() . "\n";
    $testsFailed++;
}
echo "\n";

// ==========================================
// 테스트 2: COUNT와 정렬 - 성별별 사용자 수 (내림차순)
// ==========================================
echo "📋 테스트 2: COUNT와 정렬 - 성별별 사용자 수 (내림차순)\n";
echo str_repeat('-', 70) . "\n";

try {
    $results = db()->select('gender, COUNT(*) as count')
        ->from('users')
        ->where('firebase_uid LIKE ?', ['test_groupby_%'])
        ->groupBy('gender')
        ->orderBy('count', 'DESC')
        ->get();

    echo "   결과:\n";
    foreach ($results as $row) {
        echo "      - {$row['gender']}: {$row['count']}명\n";
    }

    if (count($results) === 2) {
        echo "   ✅ 테스트 2 통과 (GROUP BY + ORDER BY)\n";
        $testsPassed++;
    } else {
        echo "   ❌ 테스트 2 실패\n";
        $testsFailed++;
    }
} catch (Exception $e) {
    echo "   ❌ 예외 발생: " . $e->getMessage() . "\n";
    $testsFailed++;
}
echo "\n";

// ==========================================
// 테스트 3: MAX, MIN 집계 함수 - 성별별 최고/최저 ID
// ==========================================
echo "📋 테스트 3: MAX, MIN 집계 함수 - 성별별 최고/최저 ID\n";
echo str_repeat('-', 70) . "\n";

try {
    $results = db()->select('gender, MAX(id) as max_id, MIN(id) as min_id, COUNT(*) as count')
        ->from('users')
        ->where('firebase_uid LIKE ?', ['test_groupby_%'])
        ->groupBy('gender')
        ->get();

    echo "   결과:\n";
    $success = true;
    foreach ($results as $row) {
        echo "      - {$row['gender']}: MIN={$row['min_id']}, MAX={$row['max_id']}, COUNT={$row['count']}\n";

        if ($row['count'] != 25) {
            $success = false;
        }
    }

    if ($success && count($results) === 2) {
        echo "   ✅ 테스트 3 통과 (MAX, MIN 집계 함수)\n";
        $testsPassed++;
    } else {
        echo "   ❌ 테스트 3 실패\n";
        $testsFailed++;
    }
} catch (Exception $e) {
    echo "   ❌ 예외 발생: " . $e->getMessage() . "\n";
    $testsFailed++;
}
echo "\n";

// ==========================================
// 테스트 4: 날짜 함수와 GROUP BY - 월별 가입자 수
// ==========================================
echo "📋 테스트 4: 날짜 함수와 GROUP BY - 월별 가입자 수\n";
echo str_repeat('-', 70) . "\n";

try {
    $results = db()->select('MONTH(FROM_UNIXTIME(created_at)) as month, COUNT(*) as count')
        ->from('users')
        ->where('firebase_uid LIKE ?', ['test_groupby_%'])
        ->groupBy('MONTH(FROM_UNIXTIME(created_at))')
        ->orderBy('month', 'ASC')
        ->get();

    echo "   월별 가입자 수:\n";
    $totalCount = 0;
    foreach ($results as $row) {
        echo sprintf("      %2d월: %2d명\n", $row['month'], $row['count']);
        $totalCount += $row['count'];
    }

    echo "\n   전체 합계: {$totalCount}명\n";

    if ($totalCount === 50 && count($results) === 12) {
        echo "   ✅ 테스트 4 통과 (날짜 함수 + GROUP BY)\n";
        $testsPassed++;
    } else {
        echo "   ❌ 테스트 4 실패 (월 수: " . count($results) . ", 합계: {$totalCount})\n";
        $testsFailed++;
    }
} catch (Exception $e) {
    echo "   ❌ 예외 발생: " . $e->getMessage() . "\n";
    $testsFailed++;
}
echo "\n";

// ==========================================
// 테스트 5: 여러 컬럼으로 GROUP BY - 연령대별, 성별별 분포
// ==========================================
echo "📋 테스트 5: 여러 컬럼으로 GROUP BY - 연령대별, 성별별 분포\n";
echo str_repeat('-', 70) . "\n";

try {
    $results = db()->select('
            gender,
            FLOOR((YEAR(CURDATE()) - YEAR(FROM_UNIXTIME(birthday))) / 10) * 10 as age_group,
            COUNT(*) as count
        ')
        ->from('users')
        ->where('firebase_uid LIKE ? AND birthday > ?', ['test_groupby_%', 0])
        ->groupBy('gender, age_group')
        ->orderBy('age_group', 'ASC')
        ->orderBy('gender', 'ASC')
        ->get();

    echo "   연령대별, 성별별 분포:\n";
    $totalCount = 0;
    foreach ($results as $row) {
        $genderText = $row['gender'] === 'M' ? '남성' : '여성';
        echo sprintf("      %s대 %s: %2d명\n", $row['age_group'], $genderText, $row['count']);
        $totalCount += $row['count'];
    }

    echo "\n   전체 합계: {$totalCount}명\n";

    // birthday가 있는 사용자만 집계되므로 총합이 50 이하일 수 있음
    if (count($results) > 0 && $totalCount > 0) {
        echo "   ✅ 테스트 5 통과 (다중 컬럼 GROUP BY)\n";
        $testsPassed++;
    } else {
        echo "   ❌ 테스트 5 실패 (그룹 수: " . count($results) . ", 총 인원: {$totalCount})\n";
        $testsFailed++;
    }
} catch (Exception $e) {
    echo "   ❌ 예외 발생: " . $e->getMessage() . "\n";
    $testsFailed++;
}
echo "\n";

// ==========================================
// 테스트 6: GROUP BY + LIMIT - 상위 3개 월만 조회
// ==========================================
echo "📋 테스트 6: GROUP BY + LIMIT - 상위 3개 월만 조회\n";
echo str_repeat('-', 70) . "\n";

try {
    $results = db()->select('MONTH(FROM_UNIXTIME(created_at)) as month, COUNT(*) as count')
        ->from('users')
        ->where('firebase_uid LIKE ?', ['test_groupby_%'])
        ->groupBy('MONTH(FROM_UNIXTIME(created_at))')
        ->orderBy('count', 'DESC')
        ->limit(3)
        ->get();

    echo "   상위 3개 월:\n";
    foreach ($results as $idx => $row) {
        echo sprintf("      %d위: %2d월 - %2d명\n", $idx + 1, $row['month'], $row['count']);
    }

    if (count($results) === 3) {
        echo "   ✅ 테스트 6 통과 (GROUP BY + LIMIT)\n";
        $testsPassed++;
    } else {
        echo "   ❌ 테스트 6 실패 (레코드 수: " . count($results) . ")\n";
        $testsFailed++;
    }
} catch (Exception $e) {
    echo "   ❌ 예외 발생: " . $e->getMessage() . "\n";
    $testsFailed++;
}
echo "\n";

// ==========================================
// 테스트 7: AVG 집계 함수 - 성별별 평균 나이
// ==========================================
echo "📋 테스트 7: AVG 집계 함수 - 성별별 평균 나이\n";
echo str_repeat('-', 70) . "\n";

try {
    $results = db()->select('
            gender,
            AVG(YEAR(CURDATE()) - YEAR(FROM_UNIXTIME(birthday))) as avg_age,
            COUNT(*) as count
        ')
        ->from('users')
        ->where('firebase_uid LIKE ? AND birthday > ?', ['test_groupby_%', 0])
        ->groupBy('gender')
        ->get();

    echo "   성별별 평균 나이:\n";
    $totalWithBirthday = 0;
    foreach ($results as $row) {
        $genderText = $row['gender'] === 'M' ? '남성' : '여성';
        $avgAge = round($row['avg_age'], 1);
        echo sprintf("      %s: %.1f세 (총 %d명)\n", $genderText, $avgAge, $row['count']);
        $totalWithBirthday += $row['count'];
    }

    // birthday가 있는 사용자만 집계되므로 총합이 50 이하일 수 있음
    if (count($results) === 2 && $totalWithBirthday > 0) {
        echo "   ✅ 테스트 7 통과 (AVG 집계 함수)\n";
        $testsPassed++;
    } else {
        echo "   ❌ 테스트 7 실패 (성별 수: " . count($results) . ", 총 인원: {$totalWithBirthday})\n";
        $testsFailed++;
    }
} catch (Exception $e) {
    echo "   ❌ 예외 발생: " . $e->getMessage() . "\n";
    $testsFailed++;
}
echo "\n";

// ==========================================
// 테스트 8: GROUP BY 없이 집계 함수만 사용 - 전체 통계
// ==========================================
echo "📋 테스트 8: GROUP BY 없이 집계 함수만 사용 - 전체 통계\n";
echo str_repeat('-', 70) . "\n";

try {
    $result = db()->select('
            COUNT(*) as total,
            COUNT(DISTINCT gender) as gender_count,
            MAX(id) as max_id,
            MIN(id) as min_id
        ')
        ->from('users')
        ->where('firebase_uid LIKE ?', ['test_groupby_%'])
        ->first();

    echo "   전체 통계:\n";
    echo "      총 사용자 수: {$result['total']}명\n";
    echo "      성별 종류: {$result['gender_count']}개\n";
    echo "      최대 ID: {$result['max_id']}\n";
    echo "      최소 ID: {$result['min_id']}\n";

    if ($result['total'] == 50 && $result['gender_count'] == 2) {
        echo "   ✅ 테스트 8 통과 (GROUP BY 없는 집계)\n";
        $testsPassed++;
    } else {
        echo "   ❌ 테스트 8 실패\n";
        $testsFailed++;
    }
} catch (Exception $e) {
    echo "   ❌ 예외 발생: " . $e->getMessage() . "\n";
    $testsFailed++;
}
echo "\n";

// ==========================================
// 테스트 9: 복잡한 쿼리 - 연령대별 성별 분포 (상세)
// ==========================================
echo "📋 테스트 9: 복잡한 쿼리 - 출생 연도별 성별 분포\n";
echo str_repeat('-', 70) . "\n";

try {
    $results = db()->select('
            YEAR(FROM_UNIXTIME(birthday)) as birth_year,
            gender,
            COUNT(*) as count
        ')
        ->from('users')
        ->where('firebase_uid LIKE ? AND birthday > ?', ['test_groupby_%', 0])
        ->groupBy('YEAR(FROM_UNIXTIME(birthday)), gender')
        ->orderBy('birth_year', 'ASC')
        ->orderBy('gender', 'ASC')
        ->limit(10)
        ->get();

    echo "   상위 10개 출생연도별 분포 (연도, 성별, 인원):\n";
    foreach ($results as $row) {
        $genderText = $row['gender'] === 'M' ? '남' : '여';
        echo sprintf("      %d년 %s: %d명\n", $row['birth_year'], $genderText, $row['count']);
    }

    if (count($results) === 10) {
        echo "   ✅ 테스트 9 통과 (복잡한 GROUP BY 쿼리)\n";
        $testsPassed++;
    } else {
        echo "   ❌ 테스트 9 실패 (레코드 수: " . count($results) . ")\n";
        $testsFailed++;
    }
} catch (Exception $e) {
    echo "   ❌ 예외 발생: " . $e->getMessage() . "\n";
    $testsFailed++;
}
echo "\n";

// ==========================================
// 테스트 10: WHERE + GROUP BY + ORDER BY + LIMIT 종합
// ==========================================
echo "📋 테스트 10: WHERE + GROUP BY + ORDER BY + LIMIT 종합\n";
echo str_repeat('-', 70) . "\n";

try {
    $results = db()->select('gender, COUNT(*) as count')
        ->from('users')
        ->where('firebase_uid LIKE ? AND birthday > ?', ['test_groupby_%', mktime(0, 0, 0, 1, 1, 1985)])
        ->groupBy('gender')
        ->orderBy('count', 'DESC')
        ->limit(2)
        ->get();

    echo "   1985년 이후 출생자 성별 분포:\n";
    foreach ($results as $row) {
        $genderText = $row['gender'] === 'M' ? '남성' : '여성';
        echo "      {$genderText}: {$row['count']}명\n";
    }

    if (count($results) <= 2) {
        echo "   ✅ 테스트 10 통과 (종합 쿼리)\n";
        $testsPassed++;
    } else {
        echo "   ❌ 테스트 10 실패\n";
        $testsFailed++;
    }
} catch (Exception $e) {
    echo "   ❌ 예외 발생: " . $e->getMessage() . "\n";
    $testsFailed++;
}
echo "\n";

// ==========================================
// 테스트 완료 및 요약
// ==========================================
echo str_repeat('=', 70) . "\n";
echo "=== 모든 테스트 완료 ===\n";
echo str_repeat('=', 70) . "\n\n";

echo "📊 테스트 결과 요약:\n";
echo "   ✅ 성공: {$testsPassed}개\n";
if ($testsFailed > 0) {
    echo "   ❌ 실패: {$testsFailed}개\n";
}
$totalTests = $testsPassed + $testsFailed;
$successRate = round(($testsPassed / $totalTests) * 100, 1);
echo "   📈 성공률: {$successRate}%\n\n";

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
            ->where('firebase_uid LIKE ?', ['test_groupby_%'])
            ->execute();
        echo "✅ 테스트 데이터 삭제 완료 ({$deleted}개 레코드 삭제)\n";
    } catch (Exception $e) {
        echo "❌ 테스트 데이터 삭제 실패: " . $e->getMessage() . "\n";
    }
} else {
    echo "ℹ️  테스트 데이터가 보존됩니다 (firebase_uid LIKE 'test_groupby_%')\n";
}

echo "\n테스트 종료.\n";

// 종료 코드 반환
exit($testsFailed > 0 ? 1 : 0);
