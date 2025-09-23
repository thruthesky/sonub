Nginx, PHP, MariaDB Setup Guide for Sonub Website Development:
- This document provides guidance on installing and configuring Nginx, PHP, and MariaDB in Ubuntu Linux environment.

## 1. System Update
```bash
sudo apt update
sudo apt upgrade -y
```
## 2. Nginx Installation
```bash
sudo apt install nginx -y
sudo systemctl start nginx
sudo systemctl enable nginx
```
## 3. PHP Installation
```bash
sudo apt install php-fpm php-mysql -y
```
## 4. MariaDB Installation
```bash
sudo apt install mariadb-server -y
sudo systemctl start mariadb
sudo systemctl enable mariadb
```

## 5. Nginx Configuration - Routing All Requests to index.php

### 5.1 Creating Nginx Configuration File
Create the file `/etc/nginx/sites-available/sonub.com.conf` and include the project's configuration file.

```nginx
# /etc/nginx/sites-available/sonub.com.conf
# Main configuration file that includes the Sonub project's nginx configuration

# Include the nginx configuration file from the project directory
# Replace <sonub-project> with the actual project path
# Examples: /home/user/sonub, /var/www/sonub, etc.
include <sonub-project>/etc/nginx/sonub.com.conf;
```

The actual Nginx configuration is maintained in your project repository at `<sonub-project>/etc/nginx/sonub.com.conf`:

```nginx
# <sonub-project>/etc/nginx/sonub.com.conf
# Actual Sonub server configuration file (managed in project repository)

server {
    listen 80;
    server_name sonub.com www.sonub.com;
    root /var/www/app/public;  # Directory path where index.php is located
    index index.php;

    # Process static files and directories, then route to index.php
    location / {
        # try_files operation order:
        # 1. $uri - If request path is an actual file, serve that file (e.g., /style.css, /image.jpg)
        # 2. $uri/ - If request path is an actual directory, serve the index file of that directory
        # 3. /index.php?$query_string - If neither above cases apply, forward to index.php while preserving query string
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM Configuration
    location ~ \.php$ {
        include fastcgi_params;
        # SCRIPT_FILENAME: Actual script file path to pass to PHP-FPM
        # $document_root = /var/www/app/public
        # $fastcgi_script_name = /index.php
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;  # Use when needed for routing
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;  # Adjust according to PHP version
        fastcgi_read_timeout 120s;
    }

    # Security: Block hidden files (.git, .env, etc.)
    location ~ /\.(?!well-known) {
        deny all;
    }
}
```

**Benefits of this approach:**
- Nginx configuration can be version-controlled with Git
- Team members can easily share the same configuration
- Configuration changes can be deployed through Git without direct server access

### 5.2 Enabling Configuration
```bash
# Create symbolic link
sudo ln -s /etc/nginx/sites-available/sonub.com.conf /etc/nginx/sites-enabled/

# Test Nginx configuration
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx
```

## 6. PHP Routing Implementation

### 6.1 index.php Example
Since all requests are forwarded to index.php, routing must be handled in PHP.

```php
<?php
// index.php - Entry point for all requests

// Extract only the path portion from request URI (exclude query string)
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Define routing table
$routes = [
    '/' => 'home.php',
    '/about' => 'about.php',
    '/contact' => 'contact.php',
    '/post/list' => 'post/list.php',
];

// Handle dynamic routing (e.g., /post/123)
if (preg_match('#^/post/(\d+)$#', $path, $matches)) {
    $postId = $matches[1];
    require_once 'post/view.php';
    exit;
}

// Handle static routing
if (isset($routes[$path])) {
    require_once $routes[$path];
} else {
    // Handle 404 page
    http_response_code(404);
    require_once '404.php';
}
```

### 6.2 Advanced Routing Example - MVC Pattern
```php
<?php
// index.php - Routing using MVC pattern

// Set up autoloader (when using Composer)
require_once __DIR__ . '/../vendor/autoload.php';

// Analyze request information
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Using Router class
class Router {
    private $routes = [];

    public function get($path, $handler) {
        $this->routes['GET'][$path] = $handler;
    }

    public function post($path, $handler) {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch($method, $uri) {
        // Check for exact match
        if (isset($this->routes[$method][$uri])) {
            return call_user_func($this->routes[$method][$uri]);
        }

        // Pattern matching (e.g., /post/{id})
        foreach ($this->routes[$method] as $pattern => $handler) {
            $pattern = str_replace('{id}', '(\d+)', $pattern);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);  // Remove first element (full match)
                return call_user_func_array($handler, $matches);
            }
        }

        // Handle 404
        http_response_code(404);
        echo "404 Page Not Found";
    }
}

// Create router instance and define routes
$router = new Router();

// GET routes
$router->get('/', function() {
    echo "Homepage";
});

$router->get('/about', function() {
    echo "About Us";
});

$router->get('/post/{id}', function($id) {
    echo "Viewing Post #" . $id;
});

// POST routes
$router->post('/login', function() {
    // Handle login
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    // ... authentication logic
});

// Execute routing
$router->dispatch($requestMethod, $requestUri);
```

### 6.3 Handling URL Parameters and Query Strings
```php
<?php
// Query strings are automatically handled via $_GET
// Example: /search?q=php&category=tutorial

$searchQuery = $_GET['q'] ?? '';
$category = $_GET['category'] ?? '';

// When using PATH_INFO
// Example: /api/users/123/posts
// Requires fastcgi_param PATH_INFO setting in Nginx
$pathInfo = $_SERVER['PATH_INFO'] ?? '';
$segments = explode('/', trim($pathInfo, '/'));
```

## 7. Directory Structure Example
```
/var/www/app/
├── public/           # Web root (Nginx document root)
│   ├── index.php    # Entry point for all requests
│   ├── css/         # Static CSS files
│   ├── js/          # Static JS files
│   └── images/      # Static image files
├── src/             # PHP source code
│   ├── Controllers/ # Controllers
│   ├── Models/      # Models
│   └── Views/       # View templates
├── vendor/          # Composer dependencies
└── config/          # Configuration files
```

## 8. Important Notes

1. **Static File Processing**: Static files like CSS, JS, and images are handled by the first option ($uri) in try_files and don't go through PHP.

2. **Security Considerations**:
   - Place sensitive files like `.env`, `.git` outside the web root or block them in Nginx
   - Position PHP files outside the public directory to prevent direct access
   - Validate user input and prevent SQL injection

3. **Performance Optimization**:
   - Static files are served directly by Nginx, making them fast
   - Use PHP autoloader to load only necessary files
   - Utilize static resource caching with caching headers

4. **Debugging Tips**:
   ```php
   // Check request information
   echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "<br>";
   echo "PATH: " . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) . "<br>";
   echo "QUERY_STRING: " . $_SERVER['QUERY_STRING'] . "<br>";
   ```