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
 * @return array Array of comment data with author information
 */
function get_comments(int $post_id): array
{

    $pdo = pdo();
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
            u.photo_url as author_photo_url
        FROM comments c
        LEFT JOIN users u ON c.user_id = u.id
        WHERE c.post_id = ?
        ORDER BY c.created_at ASC
        LIMIT ?
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$post_id, $limit]);
    $rows = $stmt->fetchAll();

    // Convert each row to CommentModel and add author info
    $comments = [];
    foreach ($rows as $row) {
        $comment = new CommentModel($row);
        $commentData = $comment->toArray();

        // Add author information
        $commentData['comment_id'] = $commentData['id'];
        $commentData['first_name'] = $row['first_name'] ?? 'Anonymous';
        $commentData['author_photo_url'] = $row['author_photo_url'];

        $comments[] = $commentData;
    }

    return $comments;
}
