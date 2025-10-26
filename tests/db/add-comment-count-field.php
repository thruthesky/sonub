<?php

/**
 * comments 테이블에 comment_count 필드 추가
 *
 * 실행 방법:
 * php tests/db/add-comment-count-field.php
 */

require_once __DIR__ . '/../../init.php';

echo "comments 테이블에 comment_count 필드 추가\n";
echo "========================================\n\n";

try {
    $pdo = pdo();

    // comment_count 필드 존재 여부 확인
    $stmt = $pdo->query("SHOW COLUMNS FROM comments LIKE 'comment_count'");
    $exists = $stmt->fetch();

    if ($exists) {
        echo "✅ comment_count 필드가 이미 존재합니다.\n";
    } else {
        echo "📋 comment_count 필드 추가 중...\n";

        $sql = "ALTER TABLE comments ADD COLUMN comment_count INT(10) UNSIGNED NOT NULL DEFAULT 0 AFTER files";
        $pdo->exec($sql);

        echo "✅ comment_count 필드가 성공적으로 추가되었습니다.\n";
    }

    // 테이블 구조 확인
    echo "\n현재 comments 테이블 구조:\n";
    echo "========================================\n";
    $stmt = $pdo->query("SHOW COLUMNS FROM comments");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($columns as $column) {
        echo sprintf("%-20s %-30s %s\n",
            $column['Field'],
            $column['Type'],
            $column['Default'] !== null ? "DEFAULT {$column['Default']}" : ''
        );
    }

    echo "\n✅ 완료!\n";

} catch (PDOException $e) {
    echo "❌ 에러 발생: " . $e->getMessage() . "\n";
    exit(1);
}
