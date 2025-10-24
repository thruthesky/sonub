<?php


function count_comments(?array $input = []): int
{
    $db = pdo();
    $post_id = $input['post_id'] ?? 0;
    $stmt = $db->prepare("SELECT COUNT(*) FROM comments WHERE post_id = ?");
    $stmt->execute([$post_id]);
    return (int)$stmt->fetchColumn();
}

/**
 * Get comments for a specific post with author information
 *
 * @param array $input Input parameters
 *   - post_id (int, required): Post ID
 *   - limit (int, optional): Number of comments to fetch (default: 50)
 * @return CommentModel[] Array of comment data with author information
 */
function get_comments(array $input): array
{

    $pdo = pdo();
    $post_id = isset($input['post_id']) ? (int)$input['post_id'] : 0;
    $limit = isset($input['limit']) ? (int)$input['limit'] : 10;

    // JOIN comments with users table to get author information
    $sql = "
        SELECT
            c.id,
            c.user_id,
            c.content,
            c.files,
            c.created_at,
            c.updated_at,
            u.first_name as first_name,
            u.photo_url as photo_url,
            u.firebase_uid as firebase_uid
        FROM comments c
        LEFT JOIN users u ON c.user_id = u.id
        WHERE c.post_id = ?
        ORDER BY c.created_at ASC
        LIMIT ?
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$post_id, $limit]);
    $rows = $stmt->fetchAll();

    return array_map(fn($row) => new CommentModel($row), $rows);
}
