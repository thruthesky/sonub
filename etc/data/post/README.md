# Random Post Generator

This directory contains a script to generate random test posts for the Sonub website.

## Purpose

The random post generator creates bulk test data to:
- Test how the website looks with lots of content
- Verify pagination works correctly
- Test search and filtering features
- Simulate a real production environment
- Performance testing with large datasets

## Files

- `generate-random-posts.php` - Main script to generate random posts

## Usage

### Generate Default Number of Posts (50)

```bash
php etc/data/post/generate-random-posts.php
```

### Generate Custom Number of Posts

```bash
php etc/data/post/generate-random-posts.php 100
```

This will generate 100 random posts.

## What the Script Does

1. **Verifies Test User**: Checks if 'banana' test user exists in the database
2. **Connects to Database**: Uses PDO for statistics queries
3. **Simulates Login**: For each post creation:
   - Calls `login_as_test_user('banana')` function from `lib/test/test.functions.php`
   - This function automatically generates session ID and sets `$_COOKIE[SESSION_ID]`
   - All posts are created by the 'banana' test user
4. **Uploads Random Images** (50% chance):
   - Downloads random images from Picsum Photos API
   - Uploads 1-3 images per post
   - Various image sizes (400-1200px width, 300-800px height)
   - Stores images in user's upload directory (`/var/uploads/{user_id}/`)
   - Returns image URLs for post attachment
5. **Creates Posts**: Uses the official `create_post()` function from `lib/post/post.crud.php`:
   - Random titles from 30 predefined templates
   - Random content from 5 templates with keyword replacements
   - Category: **my-wall** (test category)
   - Author: **banana** user (firebase_uid: 'banana')
   - **Images**: Attaches uploaded image URLs via `files` parameter (comma-separated)
   - Automatic timestamp management via `create_post()`
   - Full validation and error handling
6. **Cleans Up**: Removes temporary session after each post creation
7. **Reports Statistics**: Shows:
   - Test user information
   - Image upload progress
   - Success/failure count
   - Posts by category (my-wall)
   - Total post count in database

## Category

The script generates posts in the **my-wall** category:

- `my-wall` - Test category for random generated posts

## Example Output

```
✅ 테스트 사용자 확인: 바나나 (ID: 31)
📝 50개의 랜덤 게시글을 생성합니다...

    📸 이미지 3개 업로드 중...
    ✅ 이미지 업로드 성공: post-image-68f0b95d9de3a.jpg
    ✅ 이미지 업로드 성공: post-image-68f0b95fa962c.jpg
    ✅ 이미지 업로드 성공: post-image-68f0b96174b49.jpg
✅ 10/50 (ID: 384, Author: banana, Category: my-wall [이미지: 3개])
✅ 20/50 (ID: 394, Author: banana, Category: my-wall)
    📸 이미지 1개 업로드 중...
    ✅ 이미지 업로드 성공: post-image-68f0b9445e6ff.jpg
✅ 30/50 (ID: 404, Author: banana, Category: my-wall [이미지: 1개])
✅ 40/50 (ID: 414, Author: banana, Category: my-wall)
✅ 50/50 (ID: 424, Author: banana, Category: my-wall)

========================================
🎉 Random Post Generation Complete!
========================================
✅ Success: 50
❌ Failed: 0
========================================

📊 Post Statistics by Category:
  - my-wall: 116

📝 Total posts: 424
```

## Requirements

- PHP 7.4 or higher
- MariaDB database with `users` and `posts` tables
- **Required**: A test user with `firebase_uid = 'banana'` must exist in the database
- PDO extension enabled
- cURL extension enabled (for image download)
- Internet connection (for Picsum Photos API)

## Error Handling

The script will:
- Exit with error if no users exist
- Continue generating posts even if individual posts fail
- Report both success and failure counts
- Show detailed error messages for failed posts

## Notes

- **Uses Official API**: All posts are created using the `create_post()` function from `lib/post/post.crud.php`
- **Test Function**: Uses `login_as_test_user('banana')` from `lib/test/test.functions.php` for login simulation
- **Single Test User**: All posts are created by the 'banana' test user (firebase_uid: 'banana')
- **Test Category**: All posts are created in the `my-wall` category
- **Requires 'banana' User**: The script requires a user with firebase_uid 'banana' to exist
- **Random Images**: 50% of posts include 1-3 random images from Picsum Photos
- **Image Upload**: Uses `upload_random_image()` function to download and store images
- **Image Source**: Picsum Photos API (https://picsum.photos)
- **Image Storage**: Images are stored in `/var/uploads/{user_id}/` directory
- All posts are created with English content
- Titles are numbered to prevent duplicates
- Timestamps are Unix timestamps (integer values) managed by `create_post()`
- Full validation and error handling via `create_post()` function
- Session cleanup after each post creation

## Verification

After running the script, you can verify the posts were created:

```bash
# Check via API (my-wall category)
curl -s -k "https://local.sonub.com/api.php?func=list_posts&category=my-wall&limit=10"

# Check posts with images
curl -s -k "https://local.sonub.com/api.php?func=list_posts&category=my-wall&limit=10" | grep -o '"files":\[[^]]*\]'

# Check in database
mysql -u sonub -p sonub -e "SELECT COUNT(*) as total FROM posts WHERE category='my-wall';"

# Check posts with images in database
mysql -u sonub -p sonub -e "SELECT COUNT(*) as total FROM posts WHERE category='my-wall' AND files != '';"
```

## Image Features

- **Random Selection**: 50% chance each post gets images
- **Variable Count**: Each post with images gets 1-3 random images
- **Various Sizes**: Images range from 400-1200px width and 300-800px height
- **Picsum Photos**: High-quality placeholder images from https://picsum.photos
- **Automatic Upload**: Images are automatically downloaded and stored
- **URL Storage**: Image URLs are stored in post's `files` field (comma-separated)
- **User Directory**: Images are organized by user ID (`/var/uploads/{user_id}/`)
