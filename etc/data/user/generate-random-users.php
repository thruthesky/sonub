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

// Real profile photo IDs from Picsum Photos (always available)
$photoIds = [
    1, 10, 11, 12, 13, 14, 15, 16, 18, 20, 22, 23, 24, 25, 27, 28, 29, 30, 31, 32,
    33, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 54,
    56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78,
    82, 83, 84, 85, 88, 91, 96, 99, 101, 103, 104, 106, 111, 112, 113, 116, 119, 120,
    121, 122, 123, 124, 125, 126, 128, 129, 130, 133, 134, 135, 137, 139, 140, 141,
    142, 143, 144, 145, 146, 147
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

        // Get real profile photo URL from Picsum Photos (always available)
        $photoId = $photoIds[array_rand($photoIds)];
        $photoUrl = "https://picsum.photos/id/{$photoId}/400/400";

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
