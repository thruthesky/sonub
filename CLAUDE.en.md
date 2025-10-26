Sonub (sonub.com) Website Development Guidelines and Rules

This document specifies the guidelines and rules that must be followed when developing the Sonub website.

# Development Environment

The Sonub project runs on a Docker-based LEMP (Linux, Nginx, MySQL, PHP) stack.

## Docker Container Configuration

- `sonub-nginx`: Nginx web server
- `sonub-php`: PHP-FPM server
- `sonub-mariadb`: MariaDB database server

---

# Standard Workflow

## Step 1: Document Review and Developer Notification (Required)

1. **Search Related Documents**: Immediately read [index.md](./docs/index.md), and additionally search for related documents in `docs/**/*.md`
2. **Notify Developer**:
   ```
   📋 Reference Documents: When a developer makes a request, always and must first read the [index.md](./docs/index.md) document, review the summary and examples of docs/xxx.md detailed documents presented in this document, and refer to at least one document related to the developer's request to follow the instructions in that document.
      - Inform the developer which documents are being referenced.
   🔤 Encoding: Create/modify all files in UTF-8
   🌐 Language: All comments and documents are written in Korean
   ```

## Step 2: UTF-8 Encoding Verification and Declaration (Required)

```
✅ Starting file creation/modification with UTF-8 encoding.
✅ Ensuring all Korean text, comments, and documents are displayed correctly.
```

## Step 3: Perform Development Work

Proceed with actual development work only after completing Steps 1 and 2 above.
In particular, you must first refer to [index.md](./docs/index.md) to find documents related to the developer's question and follow the instructions in those documents.

---

# "update" Command Workflow

When a developer requests **"update"**, **comprehensively update the current file to meet all standard coding guidelines**.

## Tasks Included in "update" Command

- **Required**: Include "l10n" (multilingual translation). Add 4 language translations using `t()->inject()` function
- **Required**: Modify to meet all workflows and guidelines in CLAUDE.md
- **Required**: Update to standards by referring to `docs/**/*.md`
- **Required**: Write and execute tests for verification

## "update" Workflow Steps

### Step 1: Search and Notify Related Documents
- `docs/coding-guideline.md` - PHP, CSS, JavaScript coding guidelines
- `docs/database.md` - Database query writing guidelines
- `docs/test.md` - Test writing and execution guidelines
- `docs/translation.md` - Multilingual translation guidelines

- `docs/design-guideline.md` - Design and UI guidelines

### Step 2: Code Update
**PHP Code**:
- Use PDO directly with `pdo()` function, prevent SQL injection with placeholders (?)
- Use `throw error()` or `throw ApiException()`
- Document in PHPDoc format
- **API Functions**: Accept only one array parameter (`function func(array $input): mixed`)

**CSS/JavaScript**:
- **Style Design**: **Inline design** required using Bootstrap CSS Utility classes
  - Page files (`./page/**/*.php`): Write inline styles with Bootstrap utility classes
  - Write styles in `<style>` tags only for what's impossible with Bootstrap (prioritize Bootstrap classes when possible)
- **JavaScript**: Write all JavaScript within `<script>` tags in the corresponding PHP file
  - Page files (`./page/**/*.php`): Write within `<script>` tags
  - Widget files (`./widgets/**/*.php`): Write within `<script>` tags
  - **❌ Prohibited**: No separation into external JavaScript files
- Use Vue objects directly like `Vue.createApp()`, but separate components into separate variables and write using `Options API`
- `ready(() => { ... })` wrapper required
- Use `func()` function for API calls
- Use `tr()` function for multilingual translation in JavaScript

**Reference Documents for Design Work**:
- `docs/design/design.md` - Overall design guidelines and principles
- `docs/design/bootstrap.md` - Bootstrap CSS Utility class usage and examples
- **Must read first** and refer to the above documents when working on design-related tasks

**Multilingual Translation**:
- Define `inject_[php_file_name]_language()` function at the bottom of the file
- Inject 4 language translations using `t()->inject()` function
- Write keys in Korean

**href() Function**:
- Use `href()` function for all page links

### Step 3: Write and Execute Tests
1. **Select Test Type**:
   - PHP Unit Test: Functions, logic, DB queries
   - PHP E2E Test: Pages, UI elements, HTML
   - Playwright E2E Test: Form submission, JavaScript execution (only when PHP is not possible)

2. Write tests in `./tests` folder by referring to `docs/test.md`

### Step 4: UTF-8 Encoding Verification
```bash
file -I [file_path]
```


---

# "Document Modification" Command Workflow

When a developer requests **"document"** or **"document modification"**, find and add/modify documents in `docs/**/*.md` at the appropriate location.

## Workflow Steps

### Step 1: Document Search and Analysis
- Analyze request content: Identify topic, category
- Search and read related documents
- **Check Document Size**: Check current line count (1,000 line limit)
- Select appropriate location

### Step 2: Developer Notification
```
📋 Document Modification Plan:
- Target Document: docs/xxx.md
- Add/Modify Location: [Section Name]
```

### Step 3: Criteria for Selecting Appropriate Location
- Group by topic
- Logical order
- Maintain hierarchical structure
- Prevent duplication

### Step 4: Document Update
- Maintain markdown format consistency
- Write in Korean
- Update table of contents

### Step 5: Recheck Document Size
- **Required**: Check final line count after modification
- **If exceeds 1,000 lines**: Strong warning to developer and recommend separation

### Step 6: UTF-8 Encoding Verification

### Step 7: Completion Report
```
✅ Document modification completed
✅ Target document: docs/xxx.md
✅ Add/Modify location: [Section Name]
✅ Final line count: XXX lines
✅ UTF-8 encoding verification completed
```
---

# "Design Modification" Command Workflow

When a developer requests **"design modification"**, completely rework according to `docs/design-guideline.md`.

## Tasks Included in Design Modification
- Read `docs/design-guideline.md` document
- Simple and monotone design
- Write Bootstrap layout
- Page files: Write styles in `<style>` tags only for what's impossible with Bootstrap
- Widgets/functions: Write styles in `<style>` tags only for what's impossible with Bootstrap
- **❌ Prohibited**: No separation into external CSS/JS files

## Workflow Steps

### Step 1: Document Review and Notification
```
📋 Reference Document: docs/design-guideline.md
🎨 Design Principles: Simple, monotone, modern, Bootstrap layout
```

### Step 2: Design Modification
- Use Bootstrap layout utility classes
- Minimize shadows
- Use Bootstrap default color variables
- Sufficient spacing

See `docs/design-guideline.md` for detailed examples

### Step 3: UTF-8 Encoding Verification

---

# File Encoding and Language Rules

## UTF-8 Encoding Required
- All files must be saved in UTF-8 encoding without BOM
- Must verify before/after work
- Verification: `file -I <file_path>`

## Language Rules
- All documents and comments written in Korean
- Exceptions: Variable names, function names, technical terms

---

# Page Structure and File Loading - MPA Method

## Auto-load Items
- `init.php`, Bootstrap, Vue.js, Axios, Firebase, Font Awesome, `/css/app.css`, `/js/app.js`

**CSS/JavaScript Writing Rules**:
- ✅ Write all CSS within `<style>` tags (only when impossible with Bootstrap)
- ✅ Write all JavaScript within `<script>` tags
- ❌ No separation into external CSS/JS files

**Absolutely Prohibited**:
- No duplicate loading of `init.php`, Bootstrap, Vue.js, etc. in page files
- No `<!DOCTYPE html>`, `<html>`, `<head>`, `<body>` tags
- No separation into external CSS/JS files

## href() Function Required Use
Use `href()` function for all page links:
```php
<a href="<?= href()->user->login ?>">Login</a>
<a href="<?= href()->post->list(1, 'discussion') ?>">Discussion Board</a>
```

See `docs/coding-guideline.md` for detailed examples

---

# Design and Bootstrap Rules

- Support light mode only (dark mode prohibited)
- Maximize use of Bootstrap default color variables
- Icons: Font Awesome 7.1 Pro priority, Bootstrap Icons as alternative

See `docs/design-guideline.md` for detailed examples

---

# CSS and JavaScript Writing Rules

**🔥🔥🔥 Strongest Rule: All CSS/JavaScript must be written inside page files 🔥🔥🔥**

## Page Files (`./page/**/*.php`)
- **CSS**: Prioritize Bootstrap Utility classes, write in `<style>` tags only when impossible
- **JavaScript**: Write within `<script>` tags
- **❌ Prohibited**: No separation into external CSS/JS files

## Widget Files (`./widgets/**/*.php`)
- **CSS**: Prioritize Bootstrap Utility classes, write in `<style>` tags only when impossible
- **JavaScript**: Write within `<script>` tags
- **CSS Class Names**: Write uniquely for each widget
- **❌ Prohibited**: No separation into external CSS/JS files

See `docs/coding-guideline.md` for detailed examples

---

# JavaScript Framework - Vue.js 3.x

- Use Vue.js 3.x CDN with PHP MPA method
- Automatic cleanup on page navigation (MPA advantage)
- Each page's Vue instance is independent

---

# Test Guidelines

- Important: PHP tests must be executed directly with `php` command.

```bash
# ✅ Correct Method: Execute PHP test directly
php tests/db/db.connection.test.php
php tests/friend-and-feed/get-friends.test.php
php tests/xxx/yyy/zzz.test.php

# ❌ Wrong Method: docker exec prohibited
docker exec sonub-php php /sonub/tests/db/db.connection.test.php  # Absolutely prohibited!
docker exec sonub-php php /sonub/tests/xxx/yyy/zzz.test.php      # Absolutely prohibited!

# Playwright E2E Test execution (host environment)
npx playwright test tests/playwright/e2e/user-login.spec.ts
```

**Important Notes:**
- PHP Unit Test and PHP E2E Test **execute directly in host environment**
- Do not use `docker exec` command
- Use relative paths for test file paths (e.g., `tests/xxx/yyy.test.php`)


**Required: Read `docs/test.md` document first when working on tests**

## Test Login

**Login without SMS verification for Playwright and Chrome DevTools MCP tests:**

```
URL: https://local.sonub.com/user/login
Phone Number: banana@test.com:12345a,*
```

Enter the above phone number and click the "Send SMS Code" button to log in immediately without SMS verification.

**Usage Example (Playwright):**
```typescript
await page.goto('https://local.sonub.com/user/login');
await page.fill('input[type="tel"]', 'banana@test.com:12345a,*');
await page.click('button:has-text("Send SMS Code")');
// Auto-login completed without SMS input
```

**Usage Example (Chrome DevTools MCP):**
```javascript
// Navigate to login page
navigate_page('https://local.sonub.com/user/login');
// Enter phone number
fill(uid, 'banana@test.com:12345a,*');
// Click send button - auto-login without SMS input
click(button_uid);
```

## Automatic Test Type Selection
1. **PHP Unit Test**: Functions, logic, DB queries
2. **PHP E2E Test**: Pages, UI elements, HTML
3. **Playwright E2E Test**: Form submission, JavaScript execution (only when PHP is not possible)

## Test File Storage Location
- PHP Unit Test: `tests/[module]/[module].test.php`
- PHP E2E Test: `tests/e2e/[page-name].e2e.test.php`
- Playwright E2E Test: `tests/playwright/e2e/[page-name].spec.ts`

**Required**: Store all test files in `./tests` folder

## Test Execution

**🔥🔥🔥 Strongest Rule: PHP tests execute directly in host environment with `php` command 🔥🔥🔥**

```bash
# ✅ Correct Method: Execute PHP Unit Test directly
php tests/db/db.connection.test.php
php tests/friend-and-feed/get-friends.test.php
php tests/user/user.crud.test.php

# ✅ Correct Method: Execute PHP E2E Test directly
php tests/e2e/homepage.e2e.test.php
php tests/e2e/user-login.e2e.test.php

# ✅ Correct Method: Playwright E2E Test
npx playwright test tests/playwright/e2e/user-login.spec.ts
```

**❌ Absolutely Prohibited: docker exec command prohibited**
```bash
# ❌ Wrong Method - Never use this!
docker exec sonub-php php /sonub/tests/xxx/xxx.test.php
docker exec sonub-php php /sonub/tests/friend-and-feed/get-friends.test.php
```

**Important Notes:**
- PHP Unit Test and PHP E2E Test **must execute directly in host environment**
- **Never use** `docker exec` command
- Use relative paths for test file paths (e.g., `tests/xxx/yyy.test.php`)
- Host environment PHP connects to Docker container's MariaDB

See `docs/test.md` for detailed examples

---

# Database Development Guidelines

**Required: Read `docs/database.md` document first when working on database**

## Direct PDO Use (Top Priority)
```php
$pdo = pdo();
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();
```

- Obtain PDO object with `pdo()` function
- Prevent SQL injection with placeholders (?)
- Use `db()` query builder only in special cases
- Direct mysqli use absolutely prohibited

See `docs/database.md` for detailed examples

---

# Multilingual Translation Development Guidelines

**Required: Follow Standard Workflow in `docs/translation.md` when working on translation**

## Translation Rules
- Define `inject_[php_file_name]_language()` function at the bottom of each PHP file
- Inject translation text using `t()->inject()` function
- 4 languages (ko, en, ja, zh) required
- Write keys in Korean

```php
t()->inject([
    '이름' => ['ko' => '이름', 'en' => 'Name', 'ja' => '名前', 'zh' => '姓名'],
]);

// Usage
<?= t()->이름 ?>
```

See `docs/translation.md` for detailed examples

---

# JavaScript Special Instructions

## API Calls with func() Function
**Required**: Use `func()` function when calling PHP API functions from JavaScript

```javascript
// Direct PHP function call
const user = await func('get_user_info', { user_id: 123 });

// Include Firebase authentication
await func('create_post', {
    title: 'Post',
    auth: true
});
```

**Absolutely Prohibited**: Direct calls to `axios.post('/api.php')` or `fetch('/api.php')`

## Multilingual Translation with tr() Function
**Required**: Use `tr()` function when multilingual translation is needed in JavaScript

```javascript
// Dynamic translation in JavaScript
const message = tr({
    ko: '환영합니다',
    en: 'Welcome',
    ja: 'ようこそ',
    zh: '欢迎'
});
alert(message);
```

**Advantages:**
- ✅ Automatic translation according to user language (`window.Store.state.lang`)
- ✅ Support dynamic language switching (automatic update on language change)
- ✅ Available in Vue.js computed property

**Usage Scenarios:**
- Messages dynamically generated in JavaScript
- Text that changes based on user actions
- Reactive translation in Vue.js computed property

**PHP tr() vs JavaScript tr():**
- PHP `tr()`: Server execution time translation (static text)
- JavaScript `tr()`: Client execution time translation (dynamic text)

See `docs/javascript.md` for detailed examples

## JavaScript defer Loading and ready() Wrapper
**Required**: All JavaScript must use defer loading, `ready()` wrapper required

```html
<script defer src="/js/app.js"></script>

<script>
ready(() => {
    Vue.createApp({...}).mount('#app');
});
</script>
```

## Pre-loaded Libraries
- Firebase JS SDK, Vue.js, Axios.js are already loaded
- No duplicate loading

## firebase_ready() Function
Use `firebase_ready()` when Firebase initialization is needed:

```javascript
firebase_ready(() => {
    firebase.auth().onAuthStateChanged((user) => {
        console.log(user);
    });
});
```

## Vue.js Rules
- **Absolutely Prohibited**: `const { createApp, ref } = Vue;` destructuring assignment
- **Required**: Use Vue objects directly like `Vue.createApp()`, `Vue.ref()`, etc.
- **Required**: Mount to unique ID container (`body`, `html` prohibited)

```javascript
Vue.createApp({
    setup() {
        const count = Vue.ref(0);
        return { count };
    }
}).mount('#app'); // Mount to unique ID
```

See `docs/coding-guideline.md` for detailed examples

---

# UTF-8 Encoding Rules

**Required**: All files must be saved in UTF-8 encoding without BOM

## Verification Method
```bash
file -I docs/index.md
# Output: charset=utf-8
```

---


