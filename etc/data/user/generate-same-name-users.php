<?php
/**
 * 동일한 display_name을 가진 사용자 생성 스크립트
 *
 * 이 스크립트는 동일한 display_name "Jae"를 가진 사용자 100명을 생성합니다.
 * 각 사용자는 다른 프로필 사진을 가지며, Firebase UID는 고유합니다.
 *
 * 실행 방법:
 * php etc/data/user/generate-same-name-users.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../../init.php';

echo "========================================\n";
echo "동일한 이름 사용자 생성 시작\n";
echo "========================================\n\n";

// 설정
$first_name = 'Jae';
$last_name = 'Kim';
$middle_name = ''; // 중간 이름 (선택적)
$count = 30;

// 프로필 사진 URL 목록 (다양한 아바타 이미지)
// UI Avatars API를 사용하여 각기 다른 프로필 사진 생성
function get_profile_photo_url(int $index): string
{
    // 다양한 배경색 조합
    $backgrounds = [
        '3498db', '2ecc71', 'e74c3c', 'f39c12', '9b59b6', '1abc9c',
        'e67e22', '95a5a6', '34495e', 'c0392b', '16a085', '27ae60',
        '2980b9', '8e44ad', 'f1c40f', 'd35400', 'bdc3c7', '7f8c8d',
    ];

    // 배경색 선택 (인덱스 기반 순환)
    $bg = $backgrounds[$index % count($backgrounds)];

    // 고유한 번호를 이름으로 사용하여 각기 다른 이미지 생성
    $name = "User" . ($index + 1);

    // UI Avatars API 사용
    return "https://ui-avatars.com/api/?name=" . urlencode($name) .
           "&background=" . $bg .
           "&color=fff" .
           "&size=200" .
           "&font-size=0.4" .
           "&bold=true" .
           "&format=png";
}

echo "생성할 사용자 정보:\n";
echo "- first_name: $first_name\n";
echo "- last_name: $last_name\n";
echo "- middle_name: " . ($middle_name ?: '(없음)') . "\n";
echo "- 생성 개수: {$count}명\n\n";

// 사용자 생성
$created = 0;
$failed = 0;

for ($i = 0; $i < $count; $i++) {
    try {
        // 고유한 Firebase UID 생성 (테스트용)
        $firebase_uid = 'jae_test_' . ($i + 1) . '_' . time() . '_' . bin2hex(random_bytes(4));

        // 프로필 사진 URL
        $photo_url = get_profile_photo_url($i);

        // 사용자 생성 데이터
        $input = [
            'firebase_uid' => $firebase_uid,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'middle_name' => $middle_name,
            'photo_url' => $photo_url,
        ];

        // create_user_record() 함수 호출
        $user = create_user_record($input);

        $created++;

        // 진행 상황 출력 (10명마다)
        if (($i + 1) % 10 === 0) {
            echo "진행 중... {$created}명 생성 완료\n";
        }
    } catch (Exception $e) {
        $failed++;
        echo "⚠ 사용자 생성 실패 (#{$i}): " . $e->getMessage() . "\n";
    }
}

echo "\n========================================\n";
echo "사용자 생성 완료\n";
echo "========================================\n";
echo "성공: {$created}명\n";
echo "실패: {$failed}명\n";
echo "========================================\n\n";

// 생성된 사용자 확인
echo "생성된 사용자 샘플 (처음 5명):\n";
echo "----------------------------------------\n";

$pdo = pdo();
$stmt = $pdo->prepare("SELECT id, firebase_uid, first_name, last_name, middle_name, photo_url FROM users WHERE first_name = :first_name AND last_name = :last_name ORDER BY id DESC LIMIT 5");
$stmt->execute([':first_name' => $first_name, ':last_name' => $last_name]);
$sample_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($sample_users as $user) {
    echo "ID: {$user['id']}\n";
    echo "  - Firebase UID: {$user['firebase_uid']}\n";
    echo "  - First Name: {$user['first_name']}\n";
    echo "  - Last Name: {$user['last_name']}\n";
    echo "  - Middle Name: " . ($user['middle_name'] ?: '(없음)') . "\n";
    echo "  - Photo URL: {$user['photo_url']}\n";
    echo "\n";
}

echo "✅ 모든 작업 완료!\n";
