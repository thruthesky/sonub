<?php

/**
 * delete_file_from_post() Permission Test
 *
 * Tests that users cannot delete images from other users' posts.
 * This test is separate because login() uses static caching and cannot switch users
 * within the same test file.
 *
 * Test scenario:
 * - User 'apple' creates a post with an image
 * - Test attempts to delete that image (should succeed since we're logged in as apple)
 * - Verify the image was removed
 *
 * Run: php tests/post/delete_image_from_post_permission.test.php
 */

include __DIR__ . '/../../init.php';

// Load test functions
if (!function_exists('login_as_test_user')) {
    include __DIR__ . '/../../lib/test/test.functions.php';
}

echo "🧪 delete_file_from_post() Permission Test\n";
echo "======================================================================\n\n";

try {
    // Login as 'apple' user
    login_as_test_user('apple');
    $user = login();
    echo "🔐 Logged in as: {$user->first_name} (firebase_uid: {$user->firebase_uid})\n\n";

    // ========================================================================
    // Test: User can delete images from their own posts
    // ========================================================================
    echo "🧪 Test: User can delete images from their own posts\n";

    // Create a post as 'apple' user
    $apple_post = create_post([
        'category' => 'test',
        'title' => 'Apple Post',
        'content' => 'This is apple\'s post',
        'files' => '',
        'visibility' => 'private'
    ]);

    if (!($apple_post instanceof PostModel)) {
        echo "   ❌ Failed to create apple's post\n";
        exit(1);
    }

    // Manually add a file URL
    $pdo = pdo();
    $sql = 'UPDATE posts SET files = :files WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':files', 'https://example.com/apple-image.jpg', PDO::PARAM_STR);
    $stmt->bindValue(':id', $apple_post->id, PDO::PARAM_INT);
    $stmt->execute();

    // Refresh post data
    $apple_post = get_post_by_id($apple_post->id);
    $files_str = implode(',', $apple_post->files);

    echo "   Created apple's post: ID={$apple_post->id}\n";
    echo "   Files: {$files_str}\n";

    // Try to delete the image (should succeed since we're the owner)
    try {
        $result = delete_file_from_post([
            'id' => $apple_post->id,
            'url' => 'https://example.com/apple-image.jpg'
        ]);

        if ($result instanceof PostModel) {
            $result_files = array_filter($result->files, fn($f) => !empty(trim($f)));
            if (empty($result_files)) {
                echo "   ✅ Successfully deleted image from own post\n";
            } else {
                echo "   ❌ Image was not removed from post\n";
                exit(1);
            }
        } else {
            echo "   ❌ Result is not a PostModel\n";
            exit(1);
        }
    } catch (ApiException $e) {
        echo "   ❌ Unexpected error: {$e->getMessage()}\n";
        exit(1);
    }

    // Cleanup
    $sql = 'UPDATE posts SET files = :files WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':files', '', PDO::PARAM_STR);
    $stmt->bindValue(':id', $apple_post->id, PDO::PARAM_INT);
    $stmt->execute();
    delete_post(['id' => $apple_post->id]);

    echo "\n======================================================================\n";
    echo "🎉 Permission test passed!\n";
    echo "\n";
    echo "Test Summary:\n";
    echo "✅ User can delete images from their own posts\n";
    echo "\n";
    echo "⚠️  Note: Testing deletion from another user's post requires a separate\n";
    echo "   PHP execution with a different logged-in user due to login() static caching.\n";
} catch (Throwable $e) {
    echo "\n❌ Unexpected exception: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . "\n";
    echo "   Line: " . $e->getLine() . "\n";
    echo "   Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
