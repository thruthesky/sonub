# Design Document

## Overview

This feature adds a "My Friends" section to the top of the user list page that displays up to 5 of the logged-in user's friends. The implementation leverages existing friend and user APIs to minimize new code and maintain consistency with the current architecture.

The design follows the existing patterns in the codebase:
- Server-side data fetching with Vue.js hydration
- Reusable card components for user display
- Multi-language support through the translation system
- Graceful degradation when users are not logged in

## Architecture

### Component Structure

```
page/user/list.php
├── PHP Server-Side Logic
│   ├── Fetch friend IDs (get_friend_ids)
│   ├── Fetch friend details (list_users with ID filter)
│   └── Prepare hydration data
│
└── Vue.js Client-Side
    ├── My Friends Section (new)
    │   ├── Section Header
    │   ├── Friend Cards (reused layout)
    │   └── Empty State Message
    │
    └── All Users Section (existing)
        ├── User Cards
        └── Infinite Scroll
```

### Data Flow

1. **Server-Side (PHP)**:
   - Check if user is logged in
   - If logged in, call `get_friend_ids(['me' => $userId])`
   - Extract friend IDs from response
   - Call `list_users()` with friend IDs to get detailed user information
   - Limit to 5 friends
   - Add friends data to hydration object

2. **Client-Side (Vue.js)**:
   - Receive friends data through hydration
   - Render "My Friends" section if friends exist
   - Reuse existing user card template
   - No additional API calls needed on initial load

## Components and Interfaces

### 1. Server-Side PHP Logic

**Location**: `page/user/list.php` (top section, before existing code)

**New Code**:
```php
// Fetch my friends (if logged in)
$myFriends = [];
if (login()) {
    try {
        // Get friend IDs
        $friendIdsResult = get_friend_ids(['me' => login()->id]);
        $friendIds = $friendIdsResult['friend_ids'] ?? [];
        
        // Limit to 5 friends
        $friendIds = array_slice($friendIds, 0, 5);
        
        // Fetch friend details if we have friend IDs
        if (!empty($friendIds)) {
            // Use list_users with a custom query to fetch specific users
            $pdo = pdo();
            $placeholders = implode(',', array_fill(0, count($friendIds), '?'));
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id IN ($placeholders) LIMIT 5");
            $stmt->execute($friendIds);
            $myFriends = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) {
        // Silently fail - friends section is optional
        $myFriends = [];
    }
}

// Add to hydration data
$hydrationData['myFriends'] = $myFriends;
```

### 2. Vue.js Component

**Location**: `page/user/list.php` (Vue template section)

**New Template Section** (insert after `<h1>` and before main user list):
```html
<!-- My Friends Section (only show if logged in and has friends) -->
<div v-if="myUserId && myFriends.length > 0" class="mb-4">
    <h5 class="mb-3"><?= t()->내_친구_목록 ?></h5>
    <div class="row g-3">
        <div v-for="friend in myFriends" :key="'friend-' + friend.id" class="col-6">
            <div class="card h-100">
                <div class="card-body p-2 d-flex align-items-center">
                    <!-- Profile Photo -->
                    <a :href="`<?= href()->user->profile ?>?id=${friend.id}`" 
                       class="flex-shrink-0 me-2 text-decoration-none">
                        <img v-if="friend.photo_url"
                            :src="friend.photo_url"
                            class="rounded-circle"
                            style="width: 50px; height: 50px; object-fit: cover;"
                            :alt="friend.display_name">
                        <div v-else
                            class="rounded-circle bg-secondary bg-opacity-25 d-inline-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px;">
                            <i class="bi bi-person fs-5 text-secondary"></i>
                        </div>
                    </a>

                    <!-- User Info -->
                    <a :href="`<?= href()->user->profile ?>?id=${friend.id}`" 
                       class="flex-grow-1 min-w-0 text-decoration-none">
                        <h6 class="card-title mb-0 text-truncate text-dark">
                            {{ friend.display_name }}
                        </h6>
                        <p class="card-text text-muted mb-0" style="font-size: 0.75rem;">
                            {{ formatDate(friend.created_at) }}
                        </p>
                    </a>

                    <!-- Friend Badge (no button needed) -->
                    <div class="flex-shrink-0 ms-2">
                        <span class="badge bg-success">
                            <i class="bi bi-check-circle me-1"></i>
                            <?= t()->친구 ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Divider -->
<hr v-if="myUserId && myFriends.length > 0" class="my-4">
```

**Vue Data Update**:
```javascript
data() {
    return {
        // ... existing data ...
        myFriends: <?= json_encode($hydrationData['myFriends'], JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) ?>,
    };
}
```

### 3. Translation System

**Location**: `page/user/list.php` (inject_list_language function)

**New Translations**:
```php
'내_친구_목록' => [
    'ko' => '내 친구 목록',
    'en' => 'My Friends',
    'ja' => 'マイフレンド',
    'zh' => '我的朋友'
],
```

## Data Models

### Friend Data Structure

The friends data uses the same structure as the main user list:

```php
[
    'id' => 123,
    'firebase_uid' => 'abc123xyz',
    'display_name' => '홍길동',
    'created_at' => 1734000000,
    'updated_at' => 1734000000,
    'birthday' => 631152000,
    'gender' => 'M',
    'photo_url' => 'https://example.com/photo.jpg'
]
```

### Hydration Data Structure

```javascript
{
    users: [...],           // Existing
    total: 100,            // Existing
    currentPage: 1,        // Existing
    perPage: 20,           // Existing
    myFriends: [...]       // New - array of up to 5 friend objects
}
```

## Error Handling

### Server-Side Error Handling

1. **User Not Logged In**: 
   - No error thrown
   - `myFriends` array remains empty
   - Section not displayed

2. **API Failure (get_friend_ids)**:
   - Wrapped in try-catch
   - Silent failure - set `myFriends` to empty array
   - Log error for debugging (optional)
   - Page continues to load normally

3. **Database Query Failure**:
   - Wrapped in try-catch
   - Silent failure - set `myFriends` to empty array
   - Page continues to load normally

4. **No Friends**:
   - Not an error condition
   - `myFriends` array is empty
   - Section not displayed (handled by `v-if`)

### Client-Side Error Handling

1. **Missing Hydration Data**:
   - Vue handles gracefully with empty array default
   - Section not displayed due to `v-if` condition

2. **Invalid Friend Data**:
   - Vue's `v-for` handles empty or invalid arrays
   - Individual card rendering failures don't break the page

## Testing Strategy

### Manual Testing Checklist

1. **Logged Out User**:
   - [ ] My Friends section should not appear
   - [ ] Main user list should work normally

2. **Logged In User with No Friends**:
   - [ ] My Friends section should not appear
   - [ ] Main user list should work normally

3. **Logged In User with 1-4 Friends**:
   - [ ] My Friends section should appear
   - [ ] Should display all friends
   - [ ] Friend cards should match main list styling
   - [ ] Clicking friend should navigate to profile

4. **Logged In User with 5+ Friends**:
   - [ ] My Friends section should appear
   - [ ] Should display exactly 5 friends
   - [ ] Friend cards should display correctly

5. **Multi-Language Support**:
   - [ ] Section title should translate correctly in Korean
   - [ ] Section title should translate correctly in English
   - [ ] Section title should translate correctly in Japanese
   - [ ] Section title should translate correctly in Chinese

6. **Visual Consistency**:
   - [ ] Friend cards should match main user list cards
   - [ ] Profile photos should display correctly
   - [ ] Default avatars should display for users without photos
   - [ ] Friend badge should display correctly
   - [ ] Responsive layout should work on mobile

7. **Error Scenarios**:
   - [ ] Page should load if friend API fails
   - [ ] Page should load if database query fails
   - [ ] No console errors should appear

### Integration Testing

Since this feature integrates with existing APIs, verify:

1. **get_friend_ids() API**:
   - Returns correct friend IDs for logged-in user
   - Handles user with no friends
   - Returns empty array for invalid user

2. **list_users() / Direct Query**:
   - Returns correct user details for friend IDs
   - Handles empty ID array
   - Limits results to 5 users

3. **Translation System**:
   - New translation keys are accessible
   - Translations work in all supported languages

## Performance Considerations

1. **Database Queries**:
   - One additional query to get friend IDs (if logged in)
   - One additional query to get friend details (if has friends)
   - Both queries are limited to 5 results maximum
   - Queries only run on server-side during initial page load

2. **Client-Side Performance**:
   - No additional API calls on client-side
   - Minimal Vue.js rendering overhead (5 cards maximum)
   - Reuses existing card template and styling

3. **Caching Opportunities** (Future Enhancement):
   - Friend IDs could be cached in session
   - Friend details could be cached for short duration
   - Not implemented in initial version for simplicity

## Security Considerations

1. **Authentication**:
   - Only logged-in users see their friends
   - Uses existing `login()` function for authentication
   - No additional authentication required

2. **Authorization**:
   - Users can only see their own friends
   - Friend relationships are validated by `get_friend_ids()` API
   - No direct user ID manipulation possible

3. **Data Exposure**:
   - Only displays public user information (same as main list)
   - No sensitive data exposed
   - Photo URLs are already public

4. **XSS Prevention**:
   - Vue.js automatically escapes user-generated content
   - Display names and other text are safely rendered
   - No raw HTML injection

## Future Enhancements

1. **"View All Friends" Link**:
   - Add link to dedicated friends page
   - Show all friends with pagination

2. **Friend Sorting**:
   - Sort by most recent friendship
   - Sort by most active friends
   - Sort alphabetically

3. **Friend Activity Indicators**:
   - Show online status
   - Show recent post count
   - Show last active time

4. **Performance Optimization**:
   - Cache friend data in session
   - Use Redis for friend list caching
   - Implement lazy loading for friend photos

These enhancements are not part of the initial implementation but can be added later based on user feedback and requirements.
