<?php
/**
 * Generate random posts with comments script
 *
 * This script generates test posts with random comments for testing purposes.
 * Purpose: Test the comment loading functionality with realistic data
 *
 * Usage:
 *   php etc/data/post/generate-posts-with-comments.php
 *
 * Or specify number of posts:
 *   php etc/data/post/generate-posts-with-comments.php 10
 */

// Project initialization
require_once __DIR__ . '/../../../init.php';

// ========================================================================
// Step 1: Process command line arguments
// ========================================================================
// Number of posts to create (default: 5)
$postCount = isset($argv[1]) ? (int)$argv[1] : 5;
// Number of comments per post (random between 5-20)
$minCommentsPerPost = 5;
$maxCommentsPerPost = 20;

// ========================================================================
// Step 2: Database connection and test user verification
// ========================================================================
$pdo = pdo();

// Check if 'banana' test user exists
$bananaUser = get_user_by_firebase_uid('banana');
if (!$bananaUser) {
    echo "❌ Error: 'banana' test user does not exist.\n";
    exit(1);
}

// Check if 'apple' test user exists (for comment author)
$appleUser = get_user_by_firebase_uid('apple');
if (!$appleUser) {
    echo "❌ Error: 'apple' test user does not exist.\n";
    exit(1);
}

echo "✅ Test users confirmed:\n";
echo "   - Post author: {$bananaUser['first_name']} (ID: {$bananaUser['id']})\n";
echo "   - Comment author: {$appleUser['first_name']} (ID: {$appleUser['id']})\n";
echo "📝 Generating {$postCount} posts with {$minCommentsPerPost}-{$maxCommentsPerPost} comments each...\n\n";

// ========================================================================
// Step 3: Template data definition
// ========================================================================

// Post categories
$categories = ['story', 'ai', 'drama'];

// Random post titles
$postTitles = [
    "What do you think about web development trends?",
    "Best practices for PHP programming",
    "How to improve code quality",
    "Tips for database optimization",
    "Frontend framework comparison",
    "Backend architecture patterns",
    "Microservices vs Monolith",
    "Docker container best practices",
    "CI/CD pipeline setup guide",
    "Security considerations for web apps",
];

// Random post content
$postContents = [
    "I'd like to share my thoughts on this topic.\n\nRecently I've been studying this area and found some interesting insights.\n\nWhat are your thoughts?",
    "This is something I've been working on lately.\n\nIt was challenging at first, but I'm making good progress now.\n\nHas anyone else tried this approach?",
    "I encountered an interesting problem and wanted to share the solution.\n\nHere's what worked for me.\n\nI hope this helps someone!",
    "Just wanted to start a discussion about this.\n\nI think it's an important topic for developers.\n\nPlease share your experiences!",
];

// Random comment templates
$commentTemplates = [
    "Great post! I totally agree with your points.",
    "Thanks for sharing this. Very helpful!",
    "I had a similar experience. Here's what I learned...",
    "Interesting perspective. I think we should also consider...",
    "This is exactly what I was looking for. Thanks!",
    "Good points, but I think there's another aspect to consider.",
    "I've been working on something similar. Here's my approach...",
    "Very informative! Do you have any resources to recommend?",
    "I disagree with some points, but overall good content.",
    "This helped me solve my problem. Much appreciated!",
    "Can you elaborate more on this part?",
    "I learned something new today. Thank you!",
    "Well explained! This makes a lot of sense.",
    "I have a question about this approach...",
    "Excellent write-up! Looking forward to more posts.",
];

// ========================================================================
// Step 4: Generate posts with comments
// ========================================================================

$successCount = 0;
$failCount = 0;
$totalComments = 0;

for ($i = 1; $i <= $postCount; $i++) {
    try {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "📝 Creating post {$i}/{$postCount}\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

        // Login as banana user (post author)
        login_as_test_user('banana');

        // Random category
        $category = $categories[array_rand($categories)];

        // Random title and content
        $title = $postTitles[array_rand($postTitles)] . " #" . $i;
        $content = $postContents[array_rand($postContents)];

        // Create post
        $post = create_post([
            'category' => $category,
            'title' => $title,
            'content' => $content,
        ]);

        if (!($post instanceof PostModel)) {
            throw new Exception("Failed to create post");
        }

        echo "✅ Post created: ID {$post->id} - {$title}\n";

        // ====================================================================
        // Generate random comments for this post
        // ====================================================================
        $commentCount = mt_rand($minCommentsPerPost, $maxCommentsPerPost);
        echo "💬 Creating {$commentCount} comments...\n";

        for ($j = 1; $j <= $commentCount; $j++) {
            // Random comment content
            $commentContent = $commentTemplates[array_rand($commentTemplates)];

            // Insert comment directly using PDO
            // (since we don't have create_comment() function yet)
            $stmt = $pdo->prepare("
                INSERT INTO comments (post_id, user_id, content, files, created_at, updated_at)
                VALUES (?, ?, ?, '', ?, ?)
            ");

            $timestamp = time() - mt_rand(0, 86400 * 7); // Random time in last 7 days
            $stmt->execute([
                $post->id,
                $appleUser['id'],
                $commentContent,
                $timestamp,
                $timestamp
            ]);

            $totalComments++;

            if ($j % 5 == 0 || $j == $commentCount) {
                echo "   ✅ {$j}/{$commentCount} comments created\n";
            }
        }

        $successCount++;
        echo "✅ Post with comments completed!\n\n";

        // Cleanup session
        unset($_COOKIE[SESSION_ID]);

    } catch (Exception $e) {
        $failCount++;
        echo "❌ Failed: " . $e->getMessage() . "\n\n";

        // Cleanup session
        if (isset($_COOKIE[SESSION_ID])) {
            unset($_COOKIE[SESSION_ID]);
        }
    }
}

// ========================================================================
// Step 5: Print results
// ========================================================================

echo "\n";
echo "========================================\n";
echo "🎉 Generation Complete!\n";
echo "========================================\n";
echo "✅ Posts created: {$successCount}\n";
echo "❌ Failed: {$failCount}\n";
echo "💬 Total comments: {$totalComments}\n";
echo "========================================\n";

// ========================================================================
// Step 6: Print statistics
// ========================================================================

echo "\n📊 Database Statistics:\n";

$stmt = $pdo->query("SELECT COUNT(*) FROM posts");
$totalPosts = $stmt->fetchColumn();
echo "  - Total posts: {$totalPosts}\n";

$stmt = $pdo->query("SELECT COUNT(*) FROM comments");
$totalCommentsInDb = $stmt->fetchColumn();
echo "  - Total comments: {$totalCommentsInDb}\n";

echo "\n✅ Done!\n";
