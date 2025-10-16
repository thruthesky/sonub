<?php
/**
 * Random User Generator Script
 *
 * This script generates 100 random users with profile photos.
 *
 * Usage:
 * php etc/data/user/generate-random-users.php
 */

// Load init.php from project root
require_once dirname(dirname(dirname(__DIR__))) . '/init.php';

echo "======================================\n";
echo "Starting Random User Generation\n";
echo "======================================\n\n";

// Korean names (last names + first names)
$lastNames = ['Kim', 'Lee', 'Park', 'Choi', 'Jung', 'Kang', 'Cho', 'Yoon', 'Jang', 'Lim', 'Han', 'Oh', 'Seo', 'Shin', 'Kwon', 'Hwang', 'Ahn', 'Song', 'Jeon', 'Hong'];
$firstNames = [
    'Minjun', 'Seojun', 'Yejun', 'Doyun', 'Siwoo', 'Juwon', 'Hajun', 'Jiho', 'Jihoon', 'Junseo',
    'Seoyeon', 'Seoyoon', 'Jiwoo', 'Seohyun', 'Minseo', 'Haeun', 'Jiyu', 'Subin', 'Yeeun', 'Chaewon',
    'Eunwoo', 'Hyunwoo', 'Jihwan', 'Taeyang', 'Seunghyun', 'Yujun', 'Minjae', 'Eunho', 'Seungwoo', 'Sihyun',
    'Yerin', 'Jimin', 'Daeun', 'Yujin', 'Soyul', 'Sua', 'Ain', 'Jian', 'Yunseo', 'Sohyun'
];

// Random profile photo categories
$photoCategories = [
    'portraits', 'people', 'faces'
];

// Get PDO object
$pdo = pdo();

$successCount = 0;
$errorCount = 0;

for ($i = 1; $i <= 100; $i++) {
    try {
        // Generate random name
        $lastName = $lastNames[array_rand($lastNames)];
        $firstName = $firstNames[array_rand($firstNames)];
        $displayName = $lastName . ' ' . $firstName;

        // Check if display_name already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE display_name = ?");
        $stmt->execute([$displayName]);

        if ($stmt->fetch()) {
            // Add number if already exists
            $displayName = $displayName . rand(10, 99);
        }

        // Generate random Firebase UID (must be unique)
        $firebaseUid = 'random_user_' . uniqid() . '_' . time() . '_' . rand(1000, 9999);

        // Generate random gender
        $gender = rand(0, 1) === 0 ? 'M' : 'F';

        // Generate random birthday (between 1970 and 2005)
        $birthday = strtotime(rand(1970, 2005) . '-' . rand(1, 12) . '-' . rand(1, 28));

        // Generate random profile photo URL (Unsplash Random API)
        $category = $photoCategories[array_rand($photoCategories)];
        $photoUrl = "https://source.unsplash.com/random/400x400/?{$category}&sig=" . rand(1, 10000);

        // Current timestamp
        $now = time();

        // Insert user
        $stmt = $pdo->prepare("
            INSERT INTO users (firebase_uid, display_name, birthday, gender, photo_url, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $firebaseUid,
            $displayName,
            $birthday,
            $gender,
            $photoUrl,
            $now,
            $now
        ]);

        $userId = $pdo->lastInsertId();

        $successCount++;
        echo "Success [{$successCount}/100] User created: {$displayName} (ID: {$userId}, Gender: {$gender})\n";

    } catch (Exception $e) {
        $errorCount++;
        echo "Error [{$i}/100] User creation failed: " . $e->getMessage() . "\n";
    }
}

echo "\n======================================\n";
echo "Random User Generation Complete\n";
echo "======================================\n";
echo "Success: {$successCount} users\n";
echo "Failed: {$errorCount} users\n";
echo "======================================\n";
