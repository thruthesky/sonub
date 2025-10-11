<?php
/**
 * list_users() 함수 테스트
 *
 * 사용자 목록 조회 및 필터링 기능을 테스트합니다.
 */

// init.php 로드
require_once __DIR__ . '/../../init.php';

echo "=== list_users() 함수 종합 테스트 시작 ===\n\n";

$tests_passed = 0;
$tests_failed = 0;

// ==========================================
// 테스트 데이터 준비
// ==========================================
echo "📋 테스트 데이터 준비 중...\n";
echo str_repeat('-', 70) . "\n";

// 기존 테스트 데이터 삭제
db()->delete()
    ->from('users')
    ->where('firebase_uid LIKE ?', ['test_list_users_%'])
    ->execute();
echo "✅ 기존 테스트 데이터 정리 완료\n";

// 테스트 사용자 30명 생성
$test_users = [];
$genders = ['M', 'F'];
$names = [
    'M' => ['김철수', '이영호', '박민수', '최동욱', '정승환', '강태현', '조성민', '윤재석', '임현수', '홍길동'],
    'F' => ['김영희', '이미영', '박지은', '최수진', '정은혜', '강유진', '조민서', '윤하나', '임소영', '홍미란']
];

$current_year = (int)date('Y');
$min_birth_year = $current_year - 55; // 55세
$max_birth_year = $current_year - 20; // 20세

for ($i = 0; $i < 30; $i++) {
    $gender = $genders[$i % 2];
    $name_index = $i % 10;
    $name = $names[$gender][$name_index] . ($i >= 10 ? ($i >= 20 ? '3' : '2') : '');

    // 20세 ~ 55세 사이의 랜덤 생년월일
    $birth_year = rand($min_birth_year, $max_birth_year);
    $birthday = strtotime("$birth_year-" . rand(1, 12) . "-" . rand(1, 28));

    $data = [
        'firebase_uid' => 'test_list_users_' . str_pad($i, 3, '0', STR_PAD_LEFT),
        'display_name' => $name,
        'created_at' => time(),
        'updated_at' => time(),
        'birthday' => $birthday,
        'gender' => $gender,
    ];

    $id = db()->insert($data)->into('users');
    $test_users[] = array_merge($data, ['id' => $id]);
}

$male_count = 15;
$female_count = 15;

echo "✅ 30명의 테스트 사용자 생성 완료\n";
echo "   사용자 ID 범위: {$test_users[0]['id']} ~ {$test_users[29]['id']}\n";
echo "   성별 분포: 남성 {$male_count}명, 여성 {$female_count}명\n";
echo "   연령대: 20세 ~ 55세\n\n";

// ==========================================
// 테스트 1: 기본 목록 조회 (페이지 1)
// ==========================================
echo "📋 테스트 1: 기본 목록 조회 (페이지 1, 10명)\n";
echo str_repeat('-', 70) . "\n";

try {
    $result = list_users(['page' => 1, 'per_page' => 10]);

    echo "   페이지: {$result['page']}\n";
    echo "   페이지당 항목: {$result['per_page']}\n";
    echo "   전체 사용자 수: {$result['total']}\n";
    echo "   전체 페이지 수: {$result['total_pages']}\n";
    echo "   조회된 사용자 수: " . count($result['users']) . "명\n";

    if (count($result['users']) === 10 && $result['page'] === 1 && $result['total'] >= 30) {
        echo "   ✅ 테스트 1 통과 (기본 목록 조회)\n";
        $tests_passed++;
    } else {
        echo "   ❌ 테스트 1 실패\n";
        $tests_failed++;
    }
} catch (Exception $e) {
    echo "   ❌ 예외 발생: " . $e->getMessage() . "\n";
    $tests_failed++;
}
echo "\n";

// ==========================================
// 테스트 2: 페이지네이션 (페이지 2)
// ==========================================
echo "📋 테스트 2: 페이지네이션 (페이지 2)\n";
echo str_repeat('-', 70) . "\n";

try {
    $result = list_users(['page' => 2, 'per_page' => 10]);

    echo "   페이지: {$result['page']}\n";
    echo "   조회된 사용자 수: " . count($result['users']) . "명\n";

    if ($result['page'] === 2 && count($result['users']) === 10) {
        echo "   ✅ 테스트 2 통과 (페이지네이션)\n";
        $tests_passed++;
    } else {
        echo "   ❌ 테스트 2 실패\n";
        $tests_failed++;
    }
} catch (Exception $e) {
    echo "   ❌ 예외 발생: " . $e->getMessage() . "\n";
    $tests_failed++;
}
echo "\n";

// ==========================================
// 테스트 3: 성별 필터링 (남성)
// ==========================================
echo "📋 테스트 3: 성별 필터링 (남성만)\n";
echo str_repeat('-', 70) . "\n";

try {
    $result = list_users(['gender' => 'M', 'page' => 1, 'per_page' => 20]);

    // 전체 남성 사용자 중 테스트 데이터만 카운트
    $test_male_users = array_filter($result['users'], function($user) {
        return strpos($user['firebase_uid'], 'test_list_users_') === 0 && $user['gender'] === 'M';
    });

    echo "   조회된 사용자 수: " . count($result['users']) . "명\n";
    echo "   테스트 남성 사용자: " . count($test_male_users) . "명\n";

    // 모든 사용자가 남성인지 확인
    $all_male = true;
    foreach ($result['users'] as $user) {
        if ($user['gender'] !== 'M') {
            $all_male = false;
            break;
        }
    }

    if ($all_male && count($test_male_users) > 0) {
        echo "   ✅ 테스트 3 통과 (성별 필터링 - 남성)\n";
        $tests_passed++;
    } else {
        echo "   ❌ 테스트 3 실패\n";
        $tests_failed++;
    }
} catch (Exception $e) {
    echo "   ❌ 예외 발생: " . $e->getMessage() . "\n";
    $tests_failed++;
}
echo "\n";

// ==========================================
// 테스트 4: 성별 필터링 (여성)
// ==========================================
echo "📋 테스트 4: 성별 필터링 (여성만)\n";
echo str_repeat('-', 70) . "\n";

try {
    $result = list_users(['gender' => 'F', 'page' => 1, 'per_page' => 20]);

    // 전체 여성 사용자 중 테스트 데이터만 카운트
    $test_female_users = array_filter($result['users'], function($user) {
        return strpos($user['firebase_uid'], 'test_list_users_') === 0 && $user['gender'] === 'F';
    });

    echo "   조회된 사용자 수: " . count($result['users']) . "명\n";
    echo "   테스트 여성 사용자: " . count($test_female_users) . "명\n";

    // 모든 사용자가 여성인지 확인
    $all_female = true;
    foreach ($result['users'] as $user) {
        if ($user['gender'] !== 'F') {
            $all_female = false;
            break;
        }
    }

    if ($all_female && count($test_female_users) > 0) {
        echo "   ✅ 테스트 4 통과 (성별 필터링 - 여성)\n";
        $tests_passed++;
    } else {
        echo "   ❌ 테스트 4 실패\n";
        $tests_failed++;
    }
} catch (Exception $e) {
    echo "   ❌ 예외 발생: " . $e->getMessage() . "\n";
    $tests_failed++;
}
echo "\n";

// ==========================================
// 테스트 5: 나이 범위 필터링 (25세 ~ 35세)
// ==========================================
echo "📋 테스트 5: 나이 범위 필터링 (25세 ~ 35세)\n";
echo str_repeat('-', 70) . "\n";

try {
    $result = list_users(['age_start' => 25, 'age_end' => 35, 'page' => 1, 'per_page' => 50]);

    echo "   조회된 사용자 수: " . count($result['users']) . "명\n";

    // 나이 범위 검증
    $age_valid = true;
    $current_year = (int)date('Y');
    foreach ($result['users'] as $user) {
        if ($user['birthday'] > 0) {
            $birth_year = (int)date('Y', $user['birthday']);
            $age = $current_year - $birth_year;

            if ($age < 25 || $age > 35) {
                echo "   ⚠️ 범위 밖 나이 발견: {$age}세 (사용자 ID: {$user['id']})\n";
                $age_valid = false;
            }
        }
    }

    if ($age_valid && count($result['users']) > 0) {
        echo "   ✅ 테스트 5 통과 (나이 범위 필터링)\n";
        $tests_passed++;
    } else {
        echo "   ❌ 테스트 5 실패\n";
        $tests_failed++;
    }
} catch (Exception $e) {
    echo "   ❌ 예외 발생: " . $e->getMessage() . "\n";
    $tests_failed++;
}
echo "\n";

// ==========================================
// 테스트 6: 이름 검색 (LIKE '김%')
// ==========================================
echo "📋 테스트 6: 이름 검색 (LIKE '김%')\n";
echo str_repeat('-', 70) . "\n";

try {
    $result = list_users(['name' => '김', 'page' => 1, 'per_page' => 50]);

    echo "   조회된 사용자 수: " . count($result['users']) . "명\n";

    // 이름이 '김'으로 시작하는지 확인
    $name_valid = true;
    foreach ($result['users'] as $user) {
        if (strpos($user['display_name'], '김') !== 0) {
            echo "   ⚠️ 검색 조건 불일치: {$user['display_name']}\n";
            $name_valid = false;
        }
    }

    if ($name_valid && count($result['users']) > 0) {
        echo "   ✅ 테스트 6 통과 (이름 검색)\n";
        $tests_passed++;
    } else {
        echo "   ❌ 테스트 6 실패\n";
        $tests_failed++;
    }
} catch (Exception $e) {
    echo "   ❌ 예외 발생: " . $e->getMessage() . "\n";
    $tests_failed++;
}
echo "\n";

// ==========================================
// 테스트 7: 복합 필터링 (여성 + 25~35세 + 이름 '이')
// ==========================================
echo "📋 테스트 7: 복합 필터링 (여성 + 25~35세 + 이름 '이')\n";
echo str_repeat('-', 70) . "\n";

try {
    $result = list_users([
        'gender' => 'F',
        'age_start' => 25,
        'age_end' => 35,
        'name' => '이',
        'page' => 1,
        'per_page' => 50
    ]);

    echo "   조회된 사용자 수: " . count($result['users']) . "명\n";

    // 모든 조건 검증
    $all_valid = true;
    $current_year = (int)date('Y');

    foreach ($result['users'] as $user) {
        // 성별 확인
        if ($user['gender'] !== 'F') {
            echo "   ⚠️ 성별 불일치: {$user['gender']}\n";
            $all_valid = false;
        }

        // 나이 확인
        if ($user['birthday'] > 0) {
            $birth_year = (int)date('Y', $user['birthday']);
            $age = $current_year - $birth_year;

            if ($age < 25 || $age > 35) {
                echo "   ⚠️ 나이 범위 불일치: {$age}세\n";
                $all_valid = false;
            }
        }

        // 이름 확인
        if (strpos($user['display_name'], '이') !== 0) {
            echo "   ⚠️ 이름 불일치: {$user['display_name']}\n";
            $all_valid = false;
        }
    }

    if ($all_valid) {
        echo "   ✅ 테스트 7 통과 (복합 필터링)\n";
        $tests_passed++;
    } else {
        echo "   ❌ 테스트 7 실패\n";
        $tests_failed++;
    }
} catch (Exception $e) {
    echo "   ❌ 예외 발생: " . $e->getMessage() . "\n";
    $tests_failed++;
}
echo "\n";

// ==========================================
// 테스트 8: $_GET 직접 전달
// ==========================================
echo "📋 테스트 8: \$_GET 직접 전달 (시뮬레이션)\n";
echo str_repeat('-', 70) . "\n";

try {
    // $_GET 시뮬레이션
    $get_params = [
        'gender' => 'M',
        'age_start' => '30',
        'age_end' => '40',
        'page' => '1'
    ];

    $result = list_users(array_merge($get_params, ['per_page' => 10]));

    echo "   페이지: {$result['page']}\n";
    echo "   조회된 사용자 수: " . count($result['users']) . "명\n";

    if (count($result['users']) > 0) {
        echo "   ✅ 테스트 8 통과 (\$_GET 직접 전달)\n";
        $tests_passed++;
    } else {
        echo "   ❌ 테스트 8 실패\n";
        $tests_failed++;
    }
} catch (Exception $e) {
    echo "   ❌ 예외 발생: " . $e->getMessage() . "\n";
    $tests_failed++;
}
echo "\n";

// ==========================================
// 테스트 결과 요약
// ==========================================
echo str_repeat('=', 70) . "\n";
echo "=== 모든 테스트 완료 ===\n";
echo str_repeat('=', 70) . "\n\n";

echo "📊 테스트 결과 요약:\n";
echo "   ✅ 성공: {$tests_passed}개\n";
if ($tests_failed > 0) {
    echo "   ❌ 실패: {$tests_failed}개\n";
}
echo "   📈 성공률: " . round(($tests_passed / ($tests_passed + $tests_failed)) * 100) . "%\n\n";

// 테스트 데이터 정리 확인
echo "❓ 테스트 데이터를 삭제하시겠습니까? (y/n): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
fclose($handle);

if (trim($line) === 'y') {
    $deleted = db()->delete()
        ->from('users')
        ->where('firebase_uid LIKE ?', ['test_list_users_%'])
        ->execute();
    echo "✅ 테스트 데이터 삭제 완료 ({$deleted}개 레코드 삭제)\n";
} else {
    echo "ℹ️  테스트 데이터를 유지합니다.\n";
}

echo "\n테스트 종료.\n";
