<?php

/**
 * delete_file_from_post() Function Unit Test
 *
 * Tests for the delete_file_from_post() function in lib/post/post.crud.php
 *
 * Test scenarios:
 * 1. Delete without login (expected to fail)
 * 2. Delete with invalid post_id (expected to fail)
 * 3. Delete with missing URL (expected to fail)
 * 4. Delete from non-existent post (expected to fail)
 * 5. Delete from another user's post (expected to fail)
 * 6. Delete non-existent image URL from post (expected to fail)
 * 7. Successful image deletion from post with single image
 * 8. Successful image deletion from post with multiple images
 *
 * Run: php tests/post/delete_file_from_post.test.php
 */

include __DIR__ . '/../../init.php';

// Load test functions
if (!function_exists('login_as_test_user')) {
    include __DIR__ . '/../../lib/test/test.functions.php';
}

echo "🧪 delete_file_from_post() Function Unit Test\n";
echo "======================================================================\n\n";

try {
    // ========================================================================
    // Test 1: Delete without login (expected to fail)
    // ========================================================================
    echo "🧪 Test 1: Delete without login\n";

    try {
        $result = delete_file_from_post(['id' => 999999, 'url' => 'test.jpg']);
        echo "   ❌ No error thrown (unexpected behavior)\n";
        exit(1);
    } catch (ApiException $e) {
        if (
            str_contains($e->getMessage(), 'Login is required') ||
            str_contains($e->getMessage(), '로그인이 필요합니다')
        ) {
            echo "   ✅ Expected error: {$e->getMessage()}\n";
        } else {
            echo "   ❌ Different error: {$e->getMessage()}\n";
            exit(1);
        }
    }

    echo "\n";

    // ========================================================================
    // Test 2: Delete with invalid post_id (expected to fail)
    // ========================================================================
    echo "🧪 Test 2: Delete with invalid post_id\n";

    // Login first
    login_as_test_user('banana');

    // Test with empty post_id
    try {
        $result = delete_file_from_post(['id' => 0, 'url' => 'test.jpg']);
        echo "   ❌ No error thrown for post_id = 0\n";
        exit(1);
    } catch (ApiException $e) {
        if (
            str_contains($e->getMessage(), 'Invalid post ID') ||
            str_contains($e->getMessage(), '잘못된 게시글 ID')
        ) {
            echo "   ✅ Expected error for post_id = 0: {$e->getMessage()}\n";
        } else {
            echo "   ❌ Different error: {$e->getMessage()}\n";
            exit(1);
        }
    }

    echo "\n";

    // ========================================================================
    // Test 3: Delete with missing URL (expected to fail)
    // ========================================================================
    echo "🧪 Test 3: Delete with missing URL\n";

    try {
        $result = delete_file_from_post(['id' => 123]);
        echo "   ❌ No error thrown for missing URL\n";
        exit(1);
    } catch (ApiException $e) {
        if (
            str_contains($e->getMessage(), 'Image URL is required') ||
            str_contains($e->getMessage(), '이미지 URL이 필요합니다')
        ) {
            echo "   ✅ Expected error: {$e->getMessage()}\n";
        } else {
            echo "   ❌ Different error: {$e->getMessage()}\n";
            exit(1);
        }
    }

    echo "\n";

    // ========================================================================
    // Test 4: Delete from non-existent post (expected to fail)
    // ========================================================================
    echo "🧪 Test 4: Delete from non-existent post\n";

    $non_existent_id = 999999999;

    try {
        $result = delete_file_from_post([
            'id' => $non_existent_id,
            'url' => 'test.jpg'
        ]);
        echo "   ❌ No error thrown for non-existent post\n";
        exit(1);
    } catch (ApiException $e) {
        if (
            str_contains($e->getMessage(), 'Post not found') ||
            str_contains($e->getMessage(), '게시글을 찾을 수 없습니다')
        ) {
            echo "   ✅ Expected error: {$e->getMessage()}\n";
        } else {
            echo "   ❌ Different error: {$e->getMessage()}\n";
            exit(1);
        }
    }

    echo "\n";

    // ========================================================================
    // Test 5: Delete from another user's post (expected to fail)
    // ========================================================================
    echo "🧪 Test 5: Delete from another user's post (SKIPPED - see Test 5A)\n";
    echo "   ⚠️  Test 5 cannot work due to login() static caching\n";
    echo "   ⚠️  See test file delete_image_from_post_permission.test.php for this test\n";
    echo "\n";

    // ========================================================================
    // Test 6: Delete non-existent image URL from post (expected to fail)
    // ========================================================================
    echo "🧪 Test 6: Delete non-existent image URL from post\n";

    // Create a post with an image
    $pdo = pdo();
    $post = create_post([
        'category' => 'test',
        'title' => 'Test Post',
        'content' => 'Post with image',
        'files' => '',
        'visibility' => 'private'
    ]);

    if (!($post instanceof PostModel)) {
        echo "   ❌ Failed to create test post\n";
        exit(1);
    }

    // Manually add image URL
    $sql = 'UPDATE posts SET files = :files WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':files', 'https://example.com/image1.jpg', PDO::PARAM_STR);
    $stmt->bindValue(':id', $post->id, PDO::PARAM_INT);
    $stmt->execute();

    // Refresh post data
    $post = get_post_by_id($post->id);
    $post_files = implode(',', $post->files);
    echo "   Created test post: ID={$post->id}, Files={$post_files}\n";

    // Try to delete a non-existent image URL
    try {
        $result = delete_file_from_post([
            'id' => $post->id,
            'url' => 'https://example.com/nonexistent.jpg'
        ]);
        echo "   ❌ No error thrown for non-existent URL\n";
        exit(1);
    } catch (ApiException $e) {
        if (
            str_contains($e->getMessage(), 'Image URL not found in this post') ||
            str_contains($e->getMessage(), '이 게시글에서 이미지 URL을 찾을 수 없습니다')
        ) {
            echo "   ✅ Expected error: {$e->getMessage()}\n";
        } else {
            echo "   ❌ Different error: {$e->getMessage()}\n";
            exit(1);
        }
    }

    // Cleanup (clear files first)
    $sql = 'UPDATE posts SET files = :files WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':files', '', PDO::PARAM_STR);
    $stmt->bindValue(':id', $post->id, PDO::PARAM_INT);
    $stmt->execute();
    delete_post(['id' => $post->id]);

    echo "\n";

    // ========================================================================
    // Test 7: Successful image deletion from post with single image
    // ========================================================================
    echo "🧪 Test 7: Successful image deletion from post with single image\n";

    // Create a post with a single image
    $post = create_post([
        'category' => 'test',
        'title' => 'Single Image Post',
        'content' => 'Post with one image',
        'files' => '',
        'visibility' => 'private'
    ]);

    if (!($post instanceof PostModel)) {
        echo "   ❌ Failed to create post\n";
        exit(1);
    }

    // Manually add image URL
    $sql = 'UPDATE posts SET files = :files WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':files', 'https://example.com/single.jpg', PDO::PARAM_STR);
    $stmt->bindValue(':id', $post->id, PDO::PARAM_INT);
    $stmt->execute();

    echo "   Created post: ID={$post->id}\n";

    // Verify post has the image
    $check_before = get_post_by_id($post->id);
    if (empty($check_before->files)) {
        echo "   ❌ Post has no files before deletion\n";
        exit(1);
    }
    $files_before_str = implode(',', $check_before->files);
    echo "   ✅ Post has files before deletion: {$files_before_str}\n";

    // Delete the image
    $result = delete_file_from_post([
        'id' => $post->id,
        'url' => 'https://example.com/single.jpg'
    ]);

    if (!($result instanceof PostModel)) {
        echo "   ❌ Result is not a PostModel\n";
        exit(1);
    }

    echo "   ✅ Image deletion returned PostModel\n";

    // Verify the image was removed
    $check_after = get_post_by_id($post->id);
    $files_after_filtered = array_filter($check_after->files, fn($f) => !empty(trim($f)));
    if (empty($files_after_filtered)) {
        echo "   ✅ Image successfully removed from post (files field is empty)\n";
    } else {
        $files_after_str = implode(',', $check_after->files);
        echo "   ❌ Image still exists in post: {$files_after_str}\n";
        exit(1);
    }

    // Cleanup
    delete_post(['id' => $post->id]);

    echo "\n";

    // ========================================================================
    // Test 8: Successful image deletion from post with multiple images
    // ========================================================================
    echo "🧪 Test 8: Successful image deletion from post with multiple images\n";

    // Create a post with multiple images
    $post = create_post([
        'category' => 'test',
        'title' => 'Multiple Images Post',
        'content' => 'Post with three images',
        'files' => '',
        'visibility' => 'private'
    ]);

    if (!($post instanceof PostModel)) {
        echo "   ❌ Failed to create post\n";
        exit(1);
    }

    // Manually add multiple image URLs
    $sql = 'UPDATE posts SET files = :files WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':files', 'https://example.com/image1.jpg,https://example.com/image2.jpg,https://example.com/image3.jpg', PDO::PARAM_STR);
    $stmt->bindValue(':id', $post->id, PDO::PARAM_INT);
    $stmt->execute();

    // Refresh post data
    $post = get_post_by_id($post->id);

    echo "   Created post: ID={$post->id}\n";
    $files_before_str = implode(',', $post->files);
    echo "   Files before: {$files_before_str}\n";

    // Verify post has 3 images
    $files_before = array_filter(array_map('trim', $post->files), fn($f) => !empty($f));
    if (count($files_before) !== 3) {
        echo "   ❌ Post should have 3 images, has " . count($files_before) . "\n";
        exit(1);
    }
    echo "   ✅ Post has 3 images before deletion\n";

    // Delete the middle image
    $result = delete_file_from_post([
        'id' => $post->id,
        'url' => 'https://example.com/image2.jpg'
    ]);

    $files_after_str = implode(',', $result->files);
    echo "   Files after: {$files_after_str}\n";

    // Verify the correct image was removed
    $files_after = array_filter(array_map('trim', $result->files), fn($f) => !empty($f));

    if (count($files_after) !== 2) {
        echo "   ❌ Post should have 2 images after deletion, has " . count($files_after) . "\n";
        exit(1);
    }
    echo "   ✅ Post has 2 images after deletion\n";

    // Verify the correct images remain
    if (
        in_array('https://example.com/image1.jpg', $files_after) &&
        in_array('https://example.com/image3.jpg', $files_after)
    ) {
        echo "   ✅ Correct images remain (image1 and image3)\n";
    } else {
        echo "   ❌ Wrong images remain\n";
        echo "   Remaining: " . implode(', ', $files_after) . "\n";
        exit(1);
    }

    // Verify the deleted image is gone
    if (!in_array('https://example.com/image2.jpg', $files_after)) {
        echo "   ✅ Deleted image (image2) is not in the list\n";
    } else {
        echo "   ❌ Deleted image still exists\n";
        exit(1);
    }

    // Cleanup (clear files first)
    $sql = 'UPDATE posts SET files = :files WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':files', '', PDO::PARAM_STR);
    $stmt->bindValue(':id', $post->id, PDO::PARAM_INT);
    $stmt->execute();
    delete_post(['id' => $post->id]);

    echo "\n======================================================================\n";
    echo "🎉 All tests passed!\n";
    echo "\n";
    echo "Test Summary:\n";
    echo "✅ Test 1: Delete without login - Verified error\n";
    echo "✅ Test 2: Delete with invalid post_id - Verified error\n";
    echo "✅ Test 3: Delete with missing URL - Verified error\n";
    echo "✅ Test 4: Delete from non-existent post - Verified error\n";
    echo "⚠️  Test 5: Delete from another user's post - SKIPPED (see separate test file)\n";
    echo "✅ Test 6: Delete non-existent image URL - Verified error\n";
    echo "✅ Test 7: Delete single image - Verified success\n";
    echo "✅ Test 8: Delete from multiple images - Verified success\n";
} catch (Throwable $e) {
    echo "\n❌ Unexpected exception: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . "\n";
    echo "   Line: " . $e->getLine() . "\n";
    echo "   Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
