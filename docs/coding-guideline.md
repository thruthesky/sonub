Sonub Coding Guideline
=======================

## Table of Contents
- [General Coding Standards](#general-coding-standards)
- [Design and Styling Standards](#design-and-styling-standards)
- [Framework and Library Storage Guidelines](#framework-and-library-storage-guidelines)
  - [Complete Framework Packages](#complete-framework-packages)
  - [Single JavaScript Libraries](#single-javascript-libraries)
- [Page-specific CSS and JavaScript Auto-loading](#page-specific-css-and-javascript-auto-loading)
  - [Overview](#overview)
  - [How It Works](#how-it-works)
  - [Usage Guidelines](#usage-guidelines)
  - [Examples](#examples)
- [Firebase Integration Guidelines](#firebase-integration-guidelines)
  - [Loading Behavior](#loading-behavior)
  - [Usage in JavaScript](#usage-in-javascript)
  - [Usage in Boot Procedures](#usage-in-boot-procedures)
- [Development System Startup](#development-system-startup)
  - [Quick Start (Required Commands)](#quick-start-required-commands)
  - [Using Docker Compose](#using-docker-compose)
    - [Prerequisites](#prerequisites)
    - [Quick Start Commands](#quick-start-commands)
    - [Local Development Domain Setup](#local-development-domain-setup)
    - [Default Configuration](#default-configuration)
    - [Directory Structure](#directory-structure)
    - [Key Features](#key-features)
    - [Troubleshooting](#troubleshooting)
  - [Hot Reload Development Server](#hot-reload-development-server)
    - [Features](#features)
    - [Setup and Usage](#setup-and-usage)
    - [Configuration](#configuration)
    - [SSL Certificates](#ssl-certificates)

## General Coding Standards
- Use 4 spaces for indentation, no tabs.
- Use UTF-8 encoding without BOM.
- Write comments and documentation for complex logic.

## Design and Styling Standards
- **Light Mode Only**: Sonub website supports Light Mode only. Never implement Dark Mode features.
- **Bootstrap Colors**: Always use Bootstrap's default color classes and variables as much as possible.
- **Avoid Custom Colors**: Do not use custom HEX codes or CSS color names unless absolutely necessary.

## Framework and Library Storage Guidelines

### Complete Framework Packages
- When downloading complete framework files (e.g., Bootstrap with all its components):
  - Store in: `etc/frameworks/<framework-name>/<framework-name>-x.x.x/`
  - Example: `etc/frameworks/bootstrap/bootstrap-5.3.0/`
  - This structure maintains version control and allows multiple versions to coexist.

### Single JavaScript Libraries
- For single JavaScript file libraries (e.g., jQuery):
  - Store directly in: `/js/`
  - Example: `/js/jquery-3.7.1.min.js`
  - Use versioned filenames to track library versions.

## Page-specific CSS and JavaScript Auto-loading

### Overview
Sonub provides automatic loading functionality for page-specific CSS and JavaScript files through two helper functions defined in `lib/page/page.functions.php`. These functions automatically detect and include CSS and JS files that correspond to the current page being served, eliminating the need for manual inclusion of page-specific assets.

### How It Works
The auto-loading system uses two functions that are called in the main `index.php` file:

1. **`include_page_css()`** - Automatically includes a CSS file if it exists in the same directory as the page's PHP file
2. **`include_page_js()`** - Automatically includes a JavaScript file if it exists in the same directory as the page's PHP file

These functions work by:
1. Determining the current page path using the `page()` function
2. Checking if corresponding `.css` or `.js` files exist in the same location
3. Automatically generating and outputting the appropriate `<link>` or `<script>` tags if the files exist

### Usage Guidelines

#### Automatic Inclusion
- **No manual intervention required**: Once a CSS or JS file is created with the correct naming convention, it will be automatically loaded
- **Convention over configuration**: Simply name your files to match the PHP page file
- **Clean and maintainable**: Reduces boilerplate code and prevents forgotten includes

#### File Naming Convention
For automatic loading to work, follow this naming pattern:
- PHP page: `/page/about.php`
- CSS file: `/page/about.css` (automatically loaded if exists)
- JS file: `/page/about.js` (automatically loaded with defer attribute if exists)

For nested pages:
- PHP page: `/page/user/login.php`
- CSS file: `/page/user/login.css`
- JS file: `/page/user/login.js`

#### When to Use
**Always use page-specific CSS/JS auto-loading when:**
- Creating new pages that require custom styling or JavaScript functionality
- Building feature-specific interfaces that need isolated styles
- Developing components that should not affect global styles
- Writing page-specific JavaScript that only applies to certain routes

**Benefits:**
- Automatic code splitting - only loads CSS/JS when needed
- Better performance - reduces initial page load by loading only necessary assets
- Improved maintainability - keeps page-specific code organized with its PHP file
- No configuration needed - just create the file and it works

### Examples

#### Example 1: Creating a Support Page
When creating a new support page at `/page/support.php`:

```php
<!-- /page/support.php -->
<div class="support-container">
    <h1>Customer Support</h1>
    <!-- Page content -->
</div>
```

Simply create the corresponding files:
```css
/* /page/support.css */
.support-container {
    padding: 20px;
    background: var(--bs-light);
}
```

```javascript
// /page/support.js
ready(function() {
    // Page-specific JavaScript
    console.log('Support page loaded');
    // Initialize support-specific features
});
```

These files will be automatically included when `/support` is accessed.

#### Example 2: User Login Page
For a login page at `/page/user/login.php`:

```php
<!-- /page/user/login.php -->
<form id="login-form">
    <!-- Login form -->
</form>
```

Create:
```css
/* /page/user/login.css */
#login-form {
    max-width: 400px;
    margin: 0 auto;
}
```

```javascript
// /page/user/login.js
ready(function() {
    $('#login-form').on('submit', function(e) {
        // Handle login
    });
});
```

#### Output Example
When accessing `https://local.sonub.com/support`, the system automatically generates:
```html
<link href="/page/support.css" rel="stylesheet">
<script defer src="/page/support.js"></script>
```

These tags are injected into the `<head>` section of the page automatically by the functions called in `index.php`.

## Firebase Integration Guidelines

### Loading Behavior
Firebase is loaded **immediately** when the page loads, not as a deferred script. This means:
- Firebase SDK is loaded synchronously during page load
- Firebase is initialized immediately after loading
- Firebase services are available as soon as the DOM is ready

### Usage in JavaScript
Since Firebase is loaded and initialized immediately, you can use Firebase directly within the `ready()` function:

```javascript
// Firebase is available immediately in ready() function
ready(function() {
    // You can use Firebase services directly
    firebase.auth().onAuthStateChanged(function(user) {
        if (user) {
            console.log('User is signed in:', user.uid);
        } else {
            console.log('User is signed out');
        }
    });

    // Firestore operations
    firebase.firestore().collection('users').get()
        .then(function(querySnapshot) {
            querySnapshot.forEach(function(doc) {
                console.log(doc.id, '=>', doc.data());
            });
        });
});
```

### Usage in Boot Procedures
If you need to use Firebase in boot procedures or early initialization code, you must wrap it with the `ready()` function to ensure the DOM and Firebase are fully loaded:

```javascript
// In boot procedures or early initialization
ready(function() {
    // Initialize Firebase-dependent features
    initializeFirebaseAuth();
    setupFirestoreListeners();
    configureFirebaseMessaging();
});

// Define your Firebase initialization functions
function initializeFirebaseAuth() {
    firebase.auth().setPersistence(firebase.auth.Auth.Persistence.LOCAL);
}

function setupFirestoreListeners() {
    firebase.firestore().collection('notifications')
        .onSnapshot(function(snapshot) {
            // Handle real-time updates
        });
}
```

**Important Notes:**
- Never attempt to use Firebase before the `ready()` function
- Always check Firebase initialization status if implementing lazy-loaded features
- Firebase configuration is handled automatically by the system - do not reinitialize

## Development System Startup

### Quick Start (Required Commands)
To start Sonub development, you **must run two commands**:

```bash
# 1. Start Docker containers (Nginx + PHP-FPM)
cd ~/apps/sonub/dev/docker
docker compose up -d

# 2. Start Hot Reload server (automatic file change detection)
cd ~/apps/sonub
npm run dev
```

**Important**: Both commands must be executed to set up a complete development environment.
- `docker compose up`: Provides web server and PHP runtime environment
- `npm run dev`: Auto-refreshes browser when files change

### Using Docker Compose
Sonub development environment runs Nginx and PHP-FPM services through Docker Compose.

#### Prerequisites
- Docker and Docker Compose installation required
- Project location: `~/apps/sonub/`
- Local development domain setup required (see below)

#### Quick Start Commands
```bash
# Navigate to Docker directory
cd ~/apps/sonub/dev/docker

# Start system (Nginx + PHP-FPM)
docker compose up -d

# Check container status
docker compose ps

# View logs
docker compose logs -f

# Stop system
docker compose down

# Restart system
docker compose restart

# Start with orphan container removal
docker compose up -d --remove-orphans
```

#### Local Development Domain Setup
To use local development domain instead of localhost/127.0.0.1, add the following entry to your hosts file:

**For macOS/Linux:**
```bash
sudo nano /etc/hosts
# Add this line:
127.0.0.1 local.sonub.com
```

**For Windows:**
```bash
# Edit with administrator privileges:
# C:\Windows\System32\drivers\etc\hosts
# Add this line:
127.0.0.1 local.sonub.com
```

After adding this entry, you can access your development site at:
- http://local.sonub.com:8080
- https://local.sonub.com:8443

#### Default Configuration
- **Web Root**: `~/apps/sonub`
- **HTTP Port**: 127.0.0.1:8080
- **HTTPS Port**: 127.0.0.1:8443
- **PHP Version**: 8.3-fpm (custom build)
- **Nginx Version**: alpine (latest)
- **Network**: sonub-network (bridge)
- **Local Domain**: local.sonub.com (requires hosts file configuration)

#### Directory Structure
```
dev/docker/
├── compose.yaml         # Docker Compose configuration file
├── php.dockerfile       # PHP-FPM custom image
├── etc/
│   ├── nginx/
│   │   └── nginx.conf  # Nginx main configuration
│   └── php.ini         # PHP configuration
└── var/
    ├── log/nginx/      # Nginx logs
    └── logs/php/       # PHP logs
```

#### Key Features
- Easy service management through Docker Compose
- Automatic Nginx and PHP-FPM integration
- Real-time code reflection through volume mounts
- External log file storage

#### Troubleshooting
- Change port number in compose.yaml if port conflict occurs
- Check errors with `docker compose logs` command
- Use different port (e.g., 8080) if ERR_UNSAFE_PORT error occurs

### Hot Reload Development Server
Sonub provides a hot reload development server that automatically refreshes your browser when files change.

#### Features
- Automatic browser refresh on file changes
- CSS hot-swapping without page reload
- HTTPS support with local SSL certificates
- Watches PHP, CSS, JS, and template files

#### Setup and Usage
```bash
# Install dependencies (first time only)
npm install

# Start the hot reload server
npm run dev
```

The hot reload server will:
- Run on https://localhost:3034 (or http if SSL certificates not available)
- Monitor all project files for changes
- Automatically reload the browser when PHP files change
- Hot-swap CSS files without full page reload
- Display file change notifications in the console

#### Configuration
The hot reload server can be configured with environment variables:
- `DOMAIN`: Development domain (default: localhost)
- `PORT`: Socket server port (default: 3034)
- `USE_HTTPS`: Enable/disable HTTPS (default: true)

#### SSL Certificates
The server uses mkcert-generated SSL certificates located in:
- `./etc/server-settings/nginx/ssl/sonub/localhost-key.pem`
- `./etc/server-settings/nginx/ssl/sonub/localhost-cert.pem`

If certificates are not found, the server will automatically fall back to HTTP mode.