Design Guidelines for Sonub Website Development
================================================

## Table of Contents
- [Overview](#overview)
- [Page Structure and File Loading](#page-structure-and-file-loading)
- [Design Philosophy](#design-philosophy)
- [Bootstrap Guidelines](#bootstrap-guidelines)
- [CSS and JavaScript File Separation Rules](#css-and-javascript-file-separation-rules)
- [Icon Usage Guidelines](#icon-usage-guidelines)

## Overview
- This document specifies the design guidelines and rules for Sonub website development.
- All developers must be familiar with and comply with this document.

## Page Structure and File Loading

### MPA (Multi-Page Application) Structure

Sonub uses PHP-based MPA approach, and **all required resources are automatically loaded from `index.php`.**

### index.php Layout Structure

**🔥🔥🔥 Strongest Rule: `index.php` acts as a layout that wraps all pages 🔥🔥🔥**

`index.php` wraps all pages with the following structure:

```html
<!DOCTYPE html>
<html>
<head>
    <!-- Load CSS frameworks, icons, custom styles -->
</head>
<body>
    <!-- Header (navigation) -->
    <header id="page-header">
        <!-- Header content -->
    </header>

    <!-- Main Content Area -->
    <main>
        <?php include page() ?>  <!-- Individual page files are included here -->
    </main>

    <!-- Footer -->
    <footer id="page-footer">
        <!-- Footer content -->
    </footer>

    <!-- Load JavaScript libraries -->
</body>
</html>
```

**✅ Required Rules:**
- **All pages must have `header#page-header`, `<main>`, `footer#page-footer`**
- Individual page files (`./page/**/*.php`) are included inside the `<main>` tag
- Individual page files should only contain page-specific content
- Don't use `<!DOCTYPE html>`, `<html>`, `<head>`, `<body>` tags in individual pages as they already exist in `index.php`

### Auto-loaded Resources

The following items are automatically loaded from `index.php`, so **don't duplicate-load them in individual pages**:

#### 1. Initialization and Configuration
- ✅ `init.php` - Project initialization and configuration
- ✅ ROOT_DIR constant definition
- ✅ All libraries auto-loaded

#### 2. CSS Frameworks and Icons
- ✅ Bootstrap 5.3.8 CSS
- ✅ Bootstrap Icons 1.13.1
- ✅ Font Awesome 7.1 (Pro)
- ✅ Google Fonts (Noto Sans KR)
- ✅ `/css/app.css` - Custom styles

#### 3. JavaScript Libraries
- ✅ Vue.js 3.x (global build)
- ✅ Axios
- ✅ Bootstrap 5.3.8 JS
- ✅ `/js/app.js` - Custom scripts

#### 4. Layout Structure
- ✅ Header (navigation)
- ✅ Footer
- ✅ Sidebar (left/right)
- ✅ Main content area

#### 5. Firebase Configuration
- ✅ Firebase Authentication configuration
- ✅ Firebase initialization code

### Page PHP File Writing Rules

**🔥 Strongest Rule: In page files, only write page-specific content**

#### ✅ Correct Page File Example

```php
<?php
// page/user/profile-edit.php

// Write only page-specific logic
$user = login();
if (!$user) {
    header('Location: /page/user/login.php');
    exit;
}
?>

<!-- Write only page-specific HTML -->
<div class="container my-5">
    <h1>Edit Profile</h1>
    <form>
        <!-- Form content -->
    </form>
</div>

<!-- Write only page-specific JavaScript -->
<script>
const { createApp } = Vue;
createApp({
    // Vue app code
}).mount('#app');
</script>
```

#### ❌ Incorrect Page File Example

```php
<?php
// ❌ Prohibited: init.php is already loaded in index.php
include '../../init.php';

// ❌ Prohibited: head.php is not needed
include ROOT_DIR . '/etc/boot/head.php';
?>

<!DOCTYPE html>  <!-- ❌ Prohibited: HTML tags already exist in index.php -->
<html>
<head>
    <!-- ❌ Prohibited: These are already loaded in index.php -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <script src="/js/vue.global.js"></script>
</head>
<body>
    <!-- Page content -->
</body>
</html>

<?php
// ❌ Prohibited: foot.php is not needed
include ROOT_DIR . '/etc/boot/foot.php';
?>
```

### Page-specific Custom Resources

When page-specific CSS/JS is needed:

1. **Page-specific CSS**: Create `/page/user/profile-edit.css` → Automatically loaded
2. **Page-specific JS**: Create `/page/user/profile-edit.js` → Automatically loaded

**Important**: The following code in `index.php` automatically loads page-specific files:
```php
<?php include_page_css() ?>
<?php include_page_js() ?>
```

### Summary

| Item | Loaded in index.php | What to do in page files |
|------|-------------------|---------------------|
| init.php | ✅ Auto-loaded | ❌ Don't load |
| Bootstrap CSS/JS | ✅ Auto-loaded | ❌ Don't load |
| Vue.js | ✅ Auto-loaded | ❌ Don't load |
| Header/Footer | ✅ Auto-loaded | ❌ Don't load |
| Page content | ❌ None | ✅ Write |
| Page-specific Vue app | ❌ None | ✅ Write |

## Design Philosophy

### Light Mode Only Support
- **Important**: Sonub website supports **light mode only**
- **Never** implement dark mode features or dark mode-specific styles
- All design decisions should be optimized for light mode appearance

### Core Design Principles

**🔥🔥🔥 Strongest Rule: Sonub design must be simple, monotone, and modern 🔥🔥🔥**

#### 1. Simple and Monotone Design
- **✅ Required**: Never create flashy designs
- **✅ Required**: Prohibited complex structures or excessive decoration
- **✅ Required**: Pursue minimal design

#### 2. Modern and Simple Structure
- **✅ Required**: Write with sophisticated yet very simple structure
- **✅ Required**: Follow latest design trends but minimize complexity

#### 3. Must Use Bootstrap Layout
- **✅ Required**: Must write layout with Bootstrap
- **✅ Required**: Write layout-related utility classes as inline `class=''` attributes
- **✅ Required**: Separate non-layout Bootstrap utility classes into separate CSS files
- **Bootstrap Layout Utility Class Examples**:
  - Containers: `container`, `container-fluid`
  - Grid: `row`, `col`, `col-md-6`, `offset-md-2`
  - Flexbox: `d-flex`, `flex-column`, `gap-3`, `justify-content-center`, `align-items-center`
  - Spacing: `mb-3`, `mt-4`, `p-2`, `px-3`, `py-4`

#### 4. CSS File Separation Rules
- **✅ Page Files (`./page/**/*.php`)**: Must separate CSS into external `.css` files
  - File location: `./page/**/*.css` (same folder as page file)
  - Auto-load: Automatically loaded from `index.php`
- **✅ Widget/Function Files**: Write CSS within `<style>` tags
  - Maintain widget independence
  - Improve reusability

#### 5. Minimize Shadow
- **✅ Required**: Don't add shadow whenever possible
- **✅ Allowed**: Use only very subtle shadow when absolutely necessary
  - Example: `box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);`

#### 6. Simple Colors
- **✅ Required**: Use Bootstrap default color variables
  - `var(--bs-primary)`, `var(--bs-secondary)`, `var(--bs-light)`, `var(--bs-dark)`
  - `var(--bs-border-color)`, `var(--bs-emphasis-color)`, `var(--bs-body-color)`
- **❌ Prohibited**: Minimize HEX color codes or custom colors

#### 7. Sufficient Spacing
- **✅ Required**: Provide enough spacing between elements for a spacious design
- **✅ Required**: Small margins on page edges (left/right)
  - Example: `container-fluid px-2` or `container px-3`

### Design Modification Checklist

**When modifying design for page files (`./page/**/*.php`)**:
- [ ] Write layout with Bootstrap layout utility classes (`d-flex`, `gap-3`, `mb-4`, etc.)
- [ ] Separate non-layout styles into external `.css` files
- [ ] Apply simple and monotone design
- [ ] Minimize or remove shadow
- [ ] Use Bootstrap default color variables
- [ ] Apply sufficient spacing
- [ ] Minimize page edge margins (`px-2`, `px-3`)

**When modifying design for widget/function files**:
- [ ] Write layout with Bootstrap layout utility classes
- [ ] Write CSS within `<style>` tags
- [ ] Use widget-specific CSS class names (prevent conflicts)
- [ ] Apply simple and monotone design
- [ ] Minimize or remove shadow
- [ ] Use Bootstrap default color variables
- [ ] Apply sufficient spacing

### Design Examples

**✅ Correct Example - Page File**:

```php
<!-- ./page/user/profile.php -->
<?php
$user = login();
?>

<!-- ✅ Correct: Layout uses Bootstrap utility classes -->
<div class="container-fluid px-2 py-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <!-- profile-header class is defined in profile.css -->
            <div class="profile-header mb-4">
                <h1><?= $user->name ?></h1>
                <p><?= $user->bio ?></p>
            </div>

            <!-- profile-content class is defined in profile.css -->
            <div class="profile-content">
                <p>Content...</p>
            </div>
        </div>
    </div>
</div>
```

**External CSS File (`./page/user/profile.css`)**:

```css
/* ✅ Correct: Simple and monotone design */

/* Profile header - Minimal style */
.profile-header {
    background-color: var(--bs-light);
    border: 1px solid var(--bs-border-color);
    border-radius: 8px;
    padding: 2rem;
    /* No shadow - Simple design */
}

.profile-header h1 {
    color: var(--bs-emphasis-color);
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

.profile-header p {
    color: var(--bs-body-color);
    margin: 0;
}

/* Profile content - Simple structure */
.profile-content {
    background-color: white;
    border: 1px solid var(--bs-border-color);
    border-radius: 8px;
    padding: 2rem;
}

.profile-content p {
    color: var(--bs-body-color);
    line-height: 1.6;
}
```

**✅ Correct Example - Widget File**:

```php
<!-- ./widgets/post/post-card.php -->
<div class="d-flex flex-column gap-3 post-card-widget mb-3">
    <h3><?= $post->title ?></h3>
    <p><?= $post->content ?></p>
</div>

<style>
/* ✅ Correct: Widget CSS written within <style> tags */

/* Simple and monotone design */
.post-card-widget {
    background-color: white;
    border: 1px solid var(--bs-border-color);
    border-radius: 8px;
    padding: 1.5rem;
    /* No shadow - Minimal design */
}

.post-card-widget h3 {
    font-size: 1.25rem;
    color: var(--bs-emphasis-color);
    margin-bottom: 0.75rem;
}

.post-card-widget p {
    color: var(--bs-body-color);
    line-height: 1.6;
    margin: 0;
}
</style>
```

**❌ Incorrect Example - Flashy and Complex Design (Absolutely Prohibited)**:

```css
/* ❌ Absolutely Prohibited: Flashy and complex design */
.profile-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3), 0 2px 8px rgba(0, 0, 0, 0.2);
    border-radius: 20px;
    padding: 3rem;
    position: relative;
    overflow: hidden;
}

.profile-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%);
    animation: rotate 10s linear infinite;
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
```

## Bootstrap Guidelines

**📚 For detailed content, refer to the [Bootstrap 5 Guidelines](./bootstrap.md) document**

Detailed Bootstrap 5 guidelines (Utility CSS, colors, components, responsive design, etc.) have been separated into a separate document.

**Key Contents:**
- ✅ Bootstrap Utility CSS class usage
- ✅ Color guidelines and prohibition of custom colors
- ✅ Bootstrap component usage
- ✅ Responsive design guidelines
- ✅ Real examples and anti-patterns

**Quick Link:** [docs/design/bootstrap.md](./bootstrap.md)

---

## CSS and JavaScript File Separation Rules

**🔥🔥🔥 Strongest Rule: CSS/JS management methods differ between pages and widgets 🔥🔥🔥**

In the Sonub project, CSS and JavaScript management methods are clearly distinguished between **page files** and **widget files**.

### Page Files (`./page/**/*.php`)

**Page files are responsible for the entire page content and separate CSS and JavaScript into external files.**

#### CSS Separation Rules

**✅ Required: In page files, CSS must be separated into external files**

1. **Layout-related CSS (Written directly in HTML)**
   - Use Bootstrap 5.3.8 utility classes
   - Form like `class="d-flex flex-column gap-3 align-items-center"`
   - Layout, position, alignment, spacing, etc.

2. **Style-related CSS (Separated into external files)**
   - Create `.css` file in the same folder as the page file
   - Colors, borders, fonts, effects, and other styles
   - Automatically loaded from `index.php`

**❌ Absolutely Prohibited:**
- Using `<style>` tags within page files is prohibited
- Using inline `style` attributes is prohibited (for design-related)

**Example - Page File:**

```php
<!-- ./page/user/profile.php -->
<?php
// Page-specific logic
$user = login();
if (!$user) {
    header('Location: /user/login');
    exit;
}
?>

<!-- ✅ Correct: Layout uses Bootstrap classes -->
<div class="container my-5">
    <div class="d-flex flex-column gap-4">
        <!-- profile-header class is defined in profile.css -->
        <div class="profile-header">
            <img src="<?= $user->photo ?>" alt="Profile" class="profile-photo">
            <h1 class="profile-name"><?= $user->name ?></h1>
            <p class="profile-bio"><?= $user->bio ?></p>
        </div>

        <!-- profile-stats class is defined in profile.css -->
        <div class="d-flex justify-content-around profile-stats">
            <div class="stat-item">
                <span class="stat-number"><?= $user->post_count ?></span>
                <span class="stat-label">Posts</span>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?= $user->follower_count ?></span>
                <span class="stat-label">Followers</span>
            </div>
        </div>
    </div>
</div>

<!-- ❌ Prohibited: Using <style> tags is prohibited -->
<!-- ❌ Prohibited: Using <script> tags is prohibited -->
```

**Example - CSS File:**

```css
/* ./page/user/profile.css */
/* ✅ Correct: Styles are defined in external CSS file */

/* Profile header */
.profile-header {
    background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-info) 100%);
    padding: 40px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.profile-photo {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    border: 5px solid white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    object-fit: cover;
}

.profile-name {
    color: white;
    font-size: 2rem;
    font-weight: bold;
    margin-top: 20px;
    margin-bottom: 10px;
}

.profile-bio {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.1rem;
    line-height: 1.6;
}

/* Profile statistics */
.profile-stats {
    background-color: var(--bs-light);
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.stat-item {
    text-align: center;
    padding: 10px;
}

.stat-number {
    display: block;
    font-size: 2rem;
    font-weight: bold;
    color: var(--bs-primary);
}

.stat-label {
    display: block;
    font-size: 0.9rem;
    color: var(--bs-secondary);
    margin-top: 5px;
}
```

#### JavaScript Separation Rules

**✅ Required: In page files, JavaScript must be separated into external files**

- Create `.js` file in the same folder as the page file
- Vue apps, event handlers, and all JavaScript code
- Automatically loaded from `index.php` with `defer` attribute

**❌ Absolutely Prohibited:**
- Using `<script>` tags within page files is prohibited

**Example - JavaScript File:**

```javascript
// ./page/user/profile.js
// ✅ Correct: JavaScript is defined in external JS file

ready(() => {
    Vue.createApp({
        data() {
            return {
                user: null,
                loading: true,
                error: null,
            };
        },
        methods: {
            async loadProfile() {
                try {
                    this.loading = true;
                    const response = await func('get_user_profile', {
                        user_id: <?= $user->id ?>,
                    });

                    if (response.error_code) {
                        this.error = response.error_message;
                        return;
                    }

                    this.user = response;
                } catch (err) {
                    this.error = 'Cannot load profile.';
                } finally {
                    this.loading = false;
                }
            },

            async followUser() {
                await func('follow_user', {
                    user_id: this.user.id,
                    auth: true,
                });
                this.user.follower_count++;
            },
        },
        mounted() {
            this.loadProfile();
        },
    }).mount('#profile-app');
});
```

---

### Widget Files (`./widgets/**/*.php`)

**Widget files are reusable components and write CSS and JavaScript within the same PHP file.**

#### Widget Characteristics

- Independent UI components reused across multiple pages
- Self-contained: HTML, CSS, and JavaScript all included in one file
- Portability: Can be used immediately just by `include` from other pages

#### CSS Writing Rules

**✅ Required: Widget CSS is written within `<style>` tags**

1. **Write only widget-specific styles**
   - Global styles prohibited
   - Styles that only affect elements within the widget

2. **Use unique CSS class names**
   - Use unique prefixes to avoid conflicts with other widgets
   - Example: `.post-card-widget`, `.comment-widget`, `.user-badge-widget`

3. **Use Bootstrap variables**
   - Utilize Bootstrap CSS variables like `var(--bs-primary)`, `var(--bs-light)`, etc.

**Example - Widget File:**

```php
<!-- ./widgets/post/post-card.php -->
<?php
/**
 * Post Card Widget
 *
 * Reusable post card component
 *
 * @param array $post Post data
 *   - id: Post ID
 *   - title: Title
 *   - content: Content
 *   - author: Author
 *   - created_at: Creation time
 *   - likes: Number of likes
 */

$post_id = $post['id'];
$unique_id = "post-card-{$post_id}";
?>

<!-- ✅ Correct: Layout uses Bootstrap classes -->
<article class="d-flex flex-column gap-3 post-card-widget" id="<?= $unique_id ?>">
    <!-- Post header -->
    <header class="d-flex align-items-center gap-2 post-card-header">
        <img :src="author.photo" alt="Profile" class="author-photo">
        <div class="flex-grow-1">
            <h5 class="post-author-name">{{ author.name }}</h5>
            <time class="post-time">{{ formatTime(post.created_at) }}</time>
        </div>
        <button class="btn-more">⋮</button>
    </header>

    <!-- Post body -->
    <div class="post-card-body">
        <h3 class="post-title">{{ post.title }}</h3>
        <p class="post-content">{{ post.content }}</p>
    </div>

    <!-- Post footer -->
    <footer class="d-flex align-items-center gap-3 post-card-footer">
        <button @click="likePost" class="btn-action">
            <i class="fa-solid fa-heart" :class="{ liked: isLiked }"></i>
            <span>{{ likes }}</span>
        </button>
        <button class="btn-action">
            <i class="fa-solid fa-comment"></i>
            <span>{{ comments }}</span>
        </button>
        <button class="btn-action">
            <i class="fa-solid fa-share"></i>
        </button>
    </footer>
</article>

<!-- ✅ Correct: Widget CSS written within <style> tags -->
<style>
/* Post card widget exclusive styles */

.post-card-widget {
    background-color: white;
    border: 1px solid var(--bs-border-color);
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    transition: box-shadow 0.3s ease;
}

.post-card-widget:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Header */
.post-card-header {
    padding-bottom: 15px;
    border-bottom: 1px solid var(--bs-border-color);
}

.author-photo {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
}

.post-author-name {
    font-size: 1rem;
    font-weight: 600;
    color: var(--bs-emphasis-color);
    margin: 0;
}

.post-time {
    font-size: 0.85rem;
    color: var(--bs-secondary-color);
}

.btn-more {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: var(--bs-secondary-color);
    cursor: pointer;
    padding: 5px 10px;
}

.btn-more:hover {
    color: var(--bs-emphasis-color);
}

/* Body */
.post-card-body {
    padding: 15px 0;
}

.post-title {
    font-size: 1.25rem;
    font-weight: bold;
    color: var(--bs-emphasis-color);
    margin-bottom: 10px;
}

.post-content {
    font-size: 1rem;
    color: var(--bs-body-color);
    line-height: 1.6;
    margin: 0;
}

/* Footer */
.post-card-footer {
    padding-top: 15px;
    border-top: 1px solid var(--bs-border-color);
}

.btn-action {
    background: none;
    border: none;
    display: flex;
    align-items: center;
    gap: 5px;
    color: var(--bs-secondary-color);
    cursor: pointer;
    padding: 5px 10px;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.btn-action:hover {
    background-color: var(--bs-light);
    color: var(--bs-primary);
}

.btn-action i.liked {
    color: var(--bs-danger);
}

.btn-action span {
    font-size: 0.9rem;
}
</style>

<!-- ✅ Correct: Widget JavaScript written within <script> tags -->
<script>
ready(() => {
    Vue.createApp({
        data() {
            return {
                post: <?= json_encode($post, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) ?>,
                author: <?= json_encode($post['author'], JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) ?>,
                likes: <?= $post['likes'] ?>,
                comments: <?= $post['comments'] ?? 0 ?>,
                isLiked: false,
            };
        },
        methods: {
            async likePost() {
                try {
                    await func('like_post', {
                        post_id: this.post.id,
                        auth: true,
                    });

                    if (this.isLiked) {
                        this.likes--;
                        this.isLiked = false;
                    } else {
                        this.likes++;
                        this.isLiked = true;
                    }
                } catch (err) {
                    console.error('Like failed:', err);
                }
            },

            formatTime(timestamp) {
                // Time formatting logic
                const date = new Date(timestamp * 1000);
                return date.toLocaleDateString('ko-KR');
            },
        },
        mounted() {
            // Initialization logic
        },
    }).mount('#<?= $unique_id ?>');
});
</script>
```

#### JavaScript Writing Rules

**✅ Required: Widget JavaScript is written within `<script>` tags**

1. **Mount Vue app with unique ID**
   - Use unique ID for each widget
   - Utilize `uniqid()` function to prevent conflicts

2. **Use ready() function wrapper**
   - Write all JavaScript inside `ready(() => { ... })`
   - Ensure execution after DOM load completion

3. **Write only widget-specific logic**
   - Creating global variables prohibited
   - Write independently to not affect other widgets

---

### Page vs Widget Comparison Table

| Item | Page (`./page/**/*.php`) | Widget (`./widgets/**/*.php`) |
|------|---------------------------|---------------------------|
| **Purpose** | Entire page content | Reusable UI component |
| **CSS Location** | ✅ External file (`./page/**/*.css`) | ✅ Within same file `<style>` tag |
| **JS Location** | ✅ External file (`./page/**/*.js`) | ✅ Within same file `<script>` tag |
| **Auto Load** | ✅ Auto-loaded from `index.php` | ❌ Manual `include` required |
| **Bootstrap Classes** | ✅ Use for layout | ✅ Use for layout |
| **`<style>` Tag** | ❌ Absolutely prohibited | ✅ Must use |
| **`<script>` Tag** | ❌ Absolutely prohibited | ✅ Must use |
| **File Count** | Minimum 3 files (`.php`, `.css`, `.js`) | 1 file (`.php`) |
| **Portability** | Low (manage CSS/JS files together) | High (single file) |
| **Reusability** | Low (page-specific) | High (use in multiple pages) |
| **CSS Scope** | Entire page | Widget internal only |
| **Vue App Mount** | Page unique ID | Widget unique ID (using uniqid()) |

---

### Usage Scenarios

#### When to Use Page Files

- User profile page (`/page/user/profile.php`)
- Post list page (`/page/post/list.php`)
- Settings page (`/page/user/settings.php`)
- Login page (`/page/user/login.php`)

**Characteristics:**
- Page-specific layout and style
- Entire page corresponding to one URL
- CSS/JS files auto-loaded per page

#### When to Use Widget Files

- Post card (`/widgets/post/post-card.php`)
- Comment list (`/widgets/comment/comment-list.php`)
- User badge (`/widgets/user/user-badge.php`)
- Notification popup (`/widgets/notification/notification-popup.php`)

**Characteristics:**
- Reused across multiple pages
- Independent UI component
- All code included in one file

---

### Consequences of Violations

#### When Using `<style>` or `<script>` Tags in Page Files

**Problems:**
- Conflicts with auto-loading system
- CSS/JS files loaded multiple times
- Violates page-specific file management principle
- Inconsistent code structure
- Difficult to maintain

**Results:**
- CSS styles may be applied differently than expected
- JavaScript executed twice causing bugs
- Slower page loading speed

#### When Using External CSS/JS Files with Widget Files

**Problems:**
- Must manage CSS/JS files together when reusing widget on other pages
- Reduced widget independence
- File management becomes complex
- Decreased widget portability

**Results:**
- CSS/JS file path issues when `include` widget
- Difficult to reuse widget
- Inconvenience of copying multiple files together

---

### Work Checklist

#### When Working on Page Files

- [ ] Write only HTML and PHP logic in page files (`./page/**/*.php`)
- [ ] Use Bootstrap utility classes for layout (`class="d-flex gap-3"`)
- [ ] Write styles in `.css` file in the same folder
- [ ] Write JavaScript in `.js` file in the same folder
- [ ] No `<style>` tags within page file
- [ ] No `<script>` tags within page file
- [ ] Verify CSS file is auto-loaded (check `index.php`)
- [ ] Verify JS file is auto-loaded (check `index.php`)

#### When Working on Widget Files

- [ ] Include HTML, CSS, JS all in widget file (`./widgets/**/*.php`)
- [ ] Use Bootstrap utility classes for layout
- [ ] Write CSS within `<style>` tags
- [ ] Write JavaScript within `<script>` tags
- [ ] Use widget-specific CSS class names (prevent conflicts)
- [ ] Mount Vue app with unique ID (using `uniqid()`)
- [ ] Use `ready()` function wrapper
- [ ] Verify reusability by `include` from other pages

---

## Icon Usage Guidelines

**🔥🔥🔥 Strongest Rule: When adding icons, you must always use Font Awesome 7.1 Pro first 🔥🔥🔥**

### Available Icon Libraries

Sonub provides two icon libraries:

1. **Font Awesome 7.1 (Pro)** - **Priority 1**
2. **Bootstrap Icons 1.13.1** - **Priority 2**

**✅ Required**: Both icon libraries are automatically loaded from `index.php` and are **immediately available**.

**⚠️⚠️⚠️ Important: Icon Selection Priority**

1. **First look in Font Awesome 7.1 Pro** - In most cases, you can find the desired icon in Font Awesome
2. **Use Bootstrap Icons only if not in Font Awesome**
3. **Absolutely Prohibited**: It's a violation to use Bootstrap Icons when the icon exists in Font Awesome

### Font Awesome 7.1 Pro Usage (Priority Use)

**🔥 Top Priority: Always check Font Awesome Pro first when you need an icon**

```html
<!-- Solid style (thickest style) -->
<i class="fa-solid fa-house"></i>
<i class="fa-solid fa-user"></i>
<i class="fa-solid fa-envelope"></i>

<!-- Regular style (medium thickness) -->
<i class="fa-regular fa-heart"></i>
<i class="fa-regular fa-star"></i>

<!-- Light style (thin style - Pro only) -->
<i class="fa-light fa-house"></i>
<i class="fa-light fa-user"></i>

<!-- Thin style (thinnest style - Pro only) -->
<i class="fa-thin fa-house"></i>
<i class="fa-thin fa-user"></i>

<!-- Duotone style (two colors - Pro only) -->
<i class="fa-duotone fa-house"></i>
<i class="fa-duotone fa-user"></i>

<!-- Brands style (brand logos) -->
<i class="fa-brands fa-facebook"></i>
<i class="fa-brands fa-twitter"></i>

<!-- Size adjustment -->
<i class="fa-solid fa-house fa-2x"></i>
<i class="fa-solid fa-house fa-3x"></i>
<i class="fa-solid fa-house fa-lg"></i>

<!-- Apply colors -->
<i class="fa-solid fa-house text-primary"></i>
<i class="fa-solid fa-heart text-danger"></i>

<!-- Animation (Pro only) -->
<i class="fa-solid fa-spinner fa-spin"></i>
<i class="fa-solid fa-heart fa-beat"></i>
```

**Font Awesome 7.1 Pro Advantages:**

- **More icons**: Over 30,000 icons (Free version has 2,000)
- **More styles**: solid, regular, light, thin, duotone (Free only has solid, regular, brands)
- **Better quality**: More refined and consistent design
- **Pro-only features**: Animations, duotone, layering, etc.

### Bootstrap Icons Usage (Secondary Use)

**⚠️ Caution: Use only if the desired icon is not in Font Awesome Pro**

```html
<!-- Basic icons -->
<i class="bi bi-house"></i>
<i class="bi bi-person"></i>
<i class="bi bi-envelope"></i>

<!-- Filled icons -->
<i class="bi bi-heart-fill"></i>
<i class="bi bi-star-fill"></i>

<!-- Size adjustment -->
<i class="bi bi-house fs-1"></i>
<i class="bi bi-house fs-3"></i>
<i class="bi bi-house fs-5"></i>

<!-- Apply colors -->
<i class="bi bi-house text-primary"></i>
<i class="bi bi-heart text-danger"></i>
```

### Icon Selection Guide

**🔥🔥🔥 Must Follow: Icon Selection Order**

1. **First look in Font Awesome 7.1 Pro** (Top priority)
   - Meet most requirements with over 30,000 icons
   - Utilize various styles (solid, regular, light, thin, duotone)
   - When brand logos are needed (fa-brands)
   - When animation effects are needed
   - When more refined and consistent design is needed

2. **Use Bootstrap Icons only if not in Font Awesome** (Secondary)
   - When you can't find the desired icon in Font Awesome Pro
   - When special Bootstrap-specific icons are needed

**❌ Absolutely Prohibited:**
- Using Bootstrap Icons when the icon exists in Font Awesome Pro
- Mixing icons with the same meaning from both libraries

### Icon Usage Examples

**✅ Correct Example: Font Awesome Pro Priority Use**

```html
<!-- Use with buttons - Font Awesome Pro priority -->
<button class="btn btn-primary">
  <i class="fa-solid fa-plus me-2"></i>Add
</button>

<button class="btn btn-danger">
  <i class="fa-solid fa-trash me-2"></i>Delete
</button>

<!-- Use in navigation - Font Awesome Pro priority -->
<nav>
  <a href="#" class="nav-link">
    <i class="fa-solid fa-house me-1"></i>Home
  </a>
  <a href="#" class="nav-link">
    <i class="fa-solid fa-user me-1"></i>Profile
  </a>
  <a href="#" class="nav-link">
    <i class="fa-solid fa-envelope me-1"></i>Messages
  </a>
</nav>

<!-- Use in alert messages - Font Awesome Pro priority -->
<div class="alert alert-success">
  <i class="fa-solid fa-check-circle me-2"></i>
  Successfully saved.
</div>

<div class="alert alert-danger">
  <i class="fa-solid fa-exclamation-triangle me-2"></i>
  An error occurred.
</div>

<!-- Pro-only style usage examples -->
<button class="btn btn-outline-primary">
  <i class="fa-light fa-heart me-2"></i>Like (Light style)
</button>

<div class="card">
  <div class="card-body">
    <i class="fa-duotone fa-house fa-3x text-primary"></i>
    <p>Duotone style icon</p>
  </div>
</div>

<!-- Loading animation -->
<button class="btn btn-primary" disabled>
  <i class="fa-solid fa-spinner fa-spin me-2"></i>Loading...
</button>
```

**❌ Incorrect Example: Using Bootstrap Icons for icons that exist in Font Awesome**

```html
<!-- ❌ Prohibited: Using bi-house when fa-house exists in Font Awesome -->
<a href="#" class="nav-link">
  <i class="bi bi-house me-1"></i>Home
</a>

<!-- ✅ Correct: Use Font Awesome -->
<a href="#" class="nav-link">
  <i class="fa-solid fa-house me-1"></i>Home
</a>
```

### Precautions and Required Rules

**🔥🔥🔥 Strongest Rules:**

- **✅ Required**: When icons are needed, **always check Font Awesome 7.1 Pro first**
- **✅ Required**: Use Bootstrap Icons only if not in Font Awesome Pro
- **✅ Recommended**: Use consistent icon style throughout the project
- **✅ Recommended**: Use the same icon for the same function
- **✅ Recommended**: Utilize various styles of Font Awesome Pro (solid, regular, light, thin, duotone)

**❌ Absolutely Prohibited:**

- **❌ Prohibited**: Using Bootstrap Icons when the icon exists in Font Awesome Pro
- **❌ Prohibited**: Mixing two different icons with the same meaning is prohibited
- **❌ Prohibited**: Don't separately load icon libraries (already loaded)

**Consequences of Violations:**

- Decreased project consistency
- Waste of Font Awesome Pro subscription cost
- Loss of opportunity to use better icons