<?php
/**
 * 랜덤 사용자 데이터 생성 스크립트
 *
 * 100개의 랜덤 사용자 데이터를 생성하여 users 테이블에 삽입합니다.
 *
 * 실행 방법:
 * php etc/data/random/generate-random-users.php
 */

// init.php 로드 (프로젝트 루트 기준)
include __DIR__ . '/../../../init.php';

echo "=== 랜덤 사용자 데이터 생성 시작 ===\n\n";

// 한국 성씨 목록 (상위 20개)
$lastNames = [
    '김', '이', '박', '최', '정', '강', '조', '윤', '장', '임',
    '한', '오', '서', '신', '권', '황', '안', '송', '류', '홍'
];

// 한국 이름 (이음절)
$firstNames = [
    '민준', '서준', '예준', '도윤', '시우', '주원', '하준', '지호', '지후', '준서',
    '서현', '민서', '지우', '서윤', '지유', '채원', '하은', '수아', '소율', '윤서',
    '현우', '은우', '선우', '연우', '정우', '승우', '시후', '지환', '건우', '우진',
    '다은', '예은', '가은', '예린', '나은', '유진', '수빈', '지민', '수현', '예원'
];

// 한국 중간 이름 (선택적, 70% 확률로 비어있음)
$middleNames = ['', '', '', '', '', '', '', '민', '서', '지', '해', '윤', '원'];

// 영어 이름 목록
$englishFirstNames = [
    'James', 'John', 'Robert', 'Michael', 'William', 'David', 'Richard', 'Joseph', 'Thomas', 'Charles',
    'Mary', 'Patricia', 'Jennifer', 'Linda', 'Elizabeth', 'Barbara', 'Susan', 'Jessica', 'Sarah', 'Karen',
    'Daniel', 'Matthew', 'Christopher', 'Andrew', 'Joshua', 'Anthony', 'Mark', 'Paul', 'Steven', 'Kevin',
    'Emily', 'Ashley', 'Amanda', 'Stephanie', 'Nicole', 'Samantha', 'Rachel', 'Lauren', 'Megan', 'Hannah'
];

$englishLastNames = [
    'Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez',
    'Wilson', 'Anderson', 'Taylor', 'Thomas', 'Moore', 'Jackson', 'Martin', 'Lee', 'Thompson', 'White'
];

// 성별
$genders = ['M', 'F'];

// 프로필 사진 URL (랜덤 아바타)
$photoUrls = [
    'https://i.pravatar.cc/150?img=1',
    'https://i.pravatar.cc/150?img=2',
    'https://i.pravatar.cc/150?img=3',
    'https://i.pravatar.cc/150?img=5',
    'https://i.pravatar.cc/150?img=8',
    'https://i.pravatar.cc/150?img=9',
    'https://i.pravatar.cc/150?img=11',
    'https://i.pravatar.cc/150?img=12',
    'https://i.pravatar.cc/150?img=13',
    'https://i.pravatar.cc/150?img=14',
    'https://i.pravatar.cc/150?img=15',
    'https://i.pravatar.cc/150?img=16',
    'https://i.pravatar.cc/150?img=17',
    'https://i.pravatar.cc/150?img=18',
    'https://i.pravatar.cc/150?img=19',
    'https://i.pravatar.cc/150?img=20',
];

echo "📋 설정:\n";
echo "   - 생성할 사용자 수: 100명\n";
echo "   - Firebase UID 형식: random_user_{번호}\n";
echo "   - First Name, Last Name, Middle Name: 한국어/영어 이름 랜덤\n";
echo "   - 생년월일: 1970~2005년 사이 랜덤\n";
echo "   - 성별: 남/여 랜덤\n";
echo "   - 프로필 사진: 랜덤 아바타 또는 없음\n\n";

// 기존 random_user 데이터 확인
try {
    $existing = db()->select('COUNT(*) as count')
        ->from('users')
        ->where('firebase_uid LIKE ?', ['random_user_%'])
        ->first();

    if ($existing && $existing['count'] > 0) {
        echo "⚠️  경고: 기존 랜덤 사용자 데이터가 {$existing['count']}개 존재합니다.\n";
        echo "❓ 기존 데이터를 삭제하고 새로 생성하시겠습니까? (y/n): ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        $delete = trim(strtolower($line));
        fclose($handle);

        if ($delete === 'y' || $delete === 'yes') {
            $deleted = db()->delete()
                ->from('users')
                ->where('firebase_uid LIKE ?', ['random_user_%'])
                ->execute();
            echo "✅ 기존 데이터 {$deleted}개 삭제 완료\n\n";
        } else {
            echo "ℹ️  기존 데이터를 유지하고 추가로 생성합니다.\n\n";
        }
    }
} catch (Exception $e) {
    echo "❌ 기존 데이터 확인 실패: " . $e->getMessage() . "\n";
    exit(1);
}

// 100명의 사용자 생성
echo "📝 사용자 생성 중...\n";
echo str_repeat('-', 50) . "\n";

$successCount = 0;
$errorCount = 0;
$startTime = microtime(true);

for ($i = 1; $i <= 100; $i++) {
    try {
        // 랜덤 데이터 생성
        $useKoreanName = rand(0, 1) === 1; // 50% 확률로 한국 이름 사용

        if ($useKoreanName) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $middleName = $middleNames[array_rand($middleNames)];
        } else {
            $firstName = $englishFirstNames[array_rand($englishFirstNames)];
            $lastName = $englishLastNames[array_rand($englishLastNames)];
            $middleName = ''; // 영어 이름은 중간 이름 없음
        }

        // 중복 확인을 위한 번호 추가
        $firstName = $firstName . $i;

        // 생년월일: 1970년 ~ 2005년 사이 랜덤
        $birthYear = rand(1970, 2005);
        $birthMonth = rand(1, 12);
        $birthDay = rand(1, 28); // 간단하게 28일까지만
        $birthday = mktime(0, 0, 0, $birthMonth, $birthDay, $birthYear);

        // 성별
        $gender = $genders[array_rand($genders)];

        // 프로필 사진 (70% 확률로 추가)
        $photoUrl = '';
        if (rand(1, 10) <= 7) {
            $photoUrl = $photoUrls[array_rand($photoUrls)];
        }

        // 사용자 데이터 삽입
        $userId = db()->insert([
            'firebase_uid' => 'random_user_' . $i,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'middle_name' => $middleName,
            'birthday' => $birthday,
            'gender' => $gender,
            'photo_url' => $photoUrl,
            'created_at' => time() - rand(0, 365 * 24 * 60 * 60), // 최근 1년 내 랜덤
            'updated_at' => time(),
        ])->into('users');

        $successCount++;

        // 10명마다 진행 상황 출력
        if ($i % 10 === 0) {
            $age = date('Y') - $birthYear;
            $fullName = $firstName . ($middleName ? ' ' . $middleName : '') . ' ' . $lastName;
            echo sprintf(
                "   %3d/100: %-20s | 성별: %s | 나이: %2d세 | ID: %d\n",
                $i,
                mb_substr($fullName, 0, 20),
                $gender === 'M' ? '남' : '여',
                $age,
                $userId
            );
        }
    } catch (Exception $e) {
        $errorCount++;
        echo "   ❌ {$i}번째 사용자 생성 실패: " . $e->getMessage() . "\n";
    }
}

$endTime = microtime(true);
$elapsed = round($endTime - $startTime, 2);

echo str_repeat('-', 50) . "\n\n";

// 결과 요약
echo "=== 생성 완료 ===\n\n";
echo "📊 결과 요약:\n";
echo "   ✅ 성공: {$successCount}명\n";
if ($errorCount > 0) {
    echo "   ❌ 실패: {$errorCount}명\n";
}
echo "   ⏱️  소요 시간: {$elapsed}초\n\n";

// 생성된 데이터 샘플 조회
echo "📋 생성된 데이터 샘플 (최근 5명):\n";
echo str_repeat('-', 50) . "\n";

try {
    $samples = db()->select('*')
        ->from('users')
        ->where('firebase_uid LIKE ?', ['random_user_%'])
        ->orderBy('id', 'DESC')
        ->limit(5)
        ->get();

    foreach ($samples as $sample) {
        $age = $sample['birthday'] > 0 ? date('Y') - date('Y', $sample['birthday']) : '?';
        $hasPhoto = !empty($sample['photo_url']) ? '📷' : '  ';
        $fullName = $sample['first_name'] . ($sample['middle_name'] ? ' ' . $sample['middle_name'] : '') . ' ' . $sample['last_name'];
        echo sprintf(
            "   %s ID: %-3d | %-20s | 성별: %s | 나이: %2s세\n",
            $hasPhoto,
            $sample['id'],
            mb_substr($fullName, 0, 20),
            $sample['gender'] === 'M' ? '남' : '여',
            $age
        );
    }
} catch (Exception $e) {
    echo "   ❌ 샘플 조회 실패: " . $e->getMessage() . "\n";
}

echo "\n";

// 통계 정보
echo "📈 통계 정보:\n";
echo str_repeat('-', 50) . "\n";

try {
    // 성별 통계 - 각각 조회
    $maleCount = db()->select('COUNT(*) as count')
        ->from('users')
        ->where('firebase_uid LIKE ? AND gender = ?', ['random_user_%', 'M'])
        ->first();

    $femaleCount = db()->select('COUNT(*) as count')
        ->from('users')
        ->where('firebase_uid LIKE ? AND gender = ?', ['random_user_%', 'F'])
        ->first();

    $malePercentage = round(($maleCount['count'] / $successCount) * 100, 1);
    $femalePercentage = round(($femaleCount['count'] / $successCount) * 100, 1);

    echo "   성별 분포:\n";
    echo "      - 남성: {$maleCount['count']}명 ({$malePercentage}%)\n";
    echo "      - 여성: {$femaleCount['count']}명 ({$femalePercentage}%)\n";

    // 프로필 사진 통계
    $photoCount = db()->select('COUNT(*) as count')
        ->from('users')
        ->where('firebase_uid LIKE ? AND photo_url != ?', ['random_user_%', ''])
        ->first();

    $photoPercentage = round(($photoCount['count'] / $successCount) * 100, 1);
    echo "\n   프로필 사진:\n";
    echo "      - 있음: {$photoCount['count']}명 ({$photoPercentage}%)\n";
    echo "      - 없음: " . ($successCount - $photoCount['count']) . "명 (" . (100 - $photoPercentage) . "%)\n";
} catch (Exception $e) {
    echo "   ❌ 통계 조회 실패: " . $e->getMessage() . "\n";
}

echo "\n테스트 종료.\n";
