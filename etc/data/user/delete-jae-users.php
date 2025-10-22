<?php
/**
 * Jae 사용자 삭제 스크립트
 *
 * 실행 방법:
 * php etc/data/user/delete-jae-users.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../../init.php';

echo "========================================\n";
echo "Jae 사용자 삭제 시작\n";
echo "========================================\n\n";

$pdo = pdo();

// 삭제 전 카운트
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE first_name = 'Jae' AND last_name = 'Kim'");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$before_count = (int)$result['count'];

echo "삭제 전 Jae Kim 사용자 수: {$before_count}명\n";

// 삭제 실행
$stmt = $pdo->prepare("DELETE FROM users WHERE first_name = 'Jae' AND last_name = 'Kim'");
$stmt->execute();

// 삭제 후 카운트
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE first_name = 'Jae' AND last_name = 'Kim'");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$after_count = (int)$result['count'];

$deleted_count = $before_count - $after_count;

echo "삭제 완료: {$deleted_count}명 삭제됨\n";
echo "남은 Jae Kim 사용자 수: {$after_count}명\n\n";

echo "========================================\n";
echo "삭제 완료\n";
echo "========================================\n";
