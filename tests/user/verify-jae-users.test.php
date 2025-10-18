<?php
/**
 * Jae 사용자 생성 확인 테스트
 *
 * 실행 방법:
 * php tests/user/verify-jae-users.test.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../init.php';

echo "========================================\n";
echo "Jae 사용자 생성 확인 테스트\n";
echo "========================================\n\n";

$pdo = pdo();

// 1. Jae 사용자 수 확인
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE display_name = 'Jae'");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$count = (int)$result['count'];

echo "✅ 동일한 이름(Jae)을 가진 사용자 수: {$count}명\n\n";

// 2. 샘플 사용자 10명 확인
echo "샘플 사용자 (최근 생성된 10명):\n";
echo "----------------------------------------\n";

$stmt = $pdo->query("
    SELECT id, firebase_uid, display_name, photo_url
    FROM users
    WHERE display_name = 'Jae'
    ORDER BY id DESC
    LIMIT 10
");

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($users as $i => $user) {
    echo ($i + 1) . ". ID: {$user['id']}\n";
    echo "   - Firebase UID: {$user['firebase_uid']}\n";
    echo "   - Display Name: {$user['display_name']}\n";
    echo "   - Photo URL: {$user['photo_url']}\n\n";
}

// 3. 고유한 photo_url 개수 확인
$stmt = $pdo->query("
    SELECT COUNT(DISTINCT photo_url) as unique_count
    FROM users
    WHERE display_name = 'Jae'
");
$unique_result = $stmt->fetch(PDO::FETCH_ASSOC);
$unique_count = (int)$unique_result['unique_count'];

echo "✅ 고유한 프로필 사진 URL 개수: {$unique_count}개\n";

// 4. 고유한 firebase_uid 개수 확인
$stmt = $pdo->query("
    SELECT COUNT(DISTINCT firebase_uid) as unique_count
    FROM users
    WHERE display_name = 'Jae'
");
$unique_uid_result = $stmt->fetch(PDO::FETCH_ASSOC);
$unique_uid_count = (int)$unique_uid_result['unique_count'];

echo "✅ 고유한 Firebase UID 개수: {$unique_uid_count}개\n\n";

// 5. 검증
if ($count === 100) {
    echo "✅ 테스트 성공: 정확히 100명의 사용자가 생성되었습니다.\n";
} else {
    echo "⚠ 경고: {$count}명의 사용자가 생성되었습니다 (목표: 100명).\n";
}

if ($unique_count > 1) {
    echo "✅ 테스트 성공: 각 사용자가 다른 프로필 사진을 가지고 있습니다.\n";
} else {
    echo "❌ 테스트 실패: 모든 사용자가 동일한 프로필 사진을 가지고 있습니다.\n";
}

if ($unique_uid_count === $count) {
    echo "✅ 테스트 성공: 모든 사용자가 고유한 Firebase UID를 가지고 있습니다.\n";
} else {
    echo "❌ 테스트 실패: 중복된 Firebase UID가 있습니다.\n";
}

echo "\n========================================\n";
echo "테스트 완료\n";
echo "========================================\n";
