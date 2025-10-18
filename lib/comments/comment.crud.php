<?php


function count_comments(?array $input = []): int
{
    $db = pdo();
    $post_id = $input['post_id'] ?? 0;
    $stmt = $db->prepare("SELECT COUNT(*) FROM comments WHERE post_id = ?");
    $stmt->execute([$post_id]);
    return (int)$stmt->fetchColumn();
}
