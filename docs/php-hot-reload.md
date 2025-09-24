# PHP Hot Reload System

## Table of Contents

- [Overview](#overview)
- [Architecture](#architecture)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [File Structure](#file-structure)
- [How It Works](#how-it-works)
- [SSL Configuration](#ssl-configuration)
- [Troubleshooting](#troubleshooting)
- [Development](#development)

## Overview

The PHP Hot Reload System is a development tool that automatically refreshes your browser when PHP, CSS, JavaScript, or other project files are modified. It provides intelligent reloading:

- **CSS Hot Swap**: Updates CSS files without full page reload
- **Full Reload**: Refreshes the entire page for PHP, JavaScript, and other file changes
- **SSL Support**: Works with HTTPS for secure local development
- **Multi-domain Support**: Handles both localhost and custom domains (e.g., local.sonub.com)

## Architecture

The system consists of two main components:

1. **Server (`etc/php-hot-reload-server.js`)**: Node.js WebSocket server that watches for file changes
2. **Client (`etc/php-hot-reload-client.php`)**: Browser-side JavaScript that connects to the server and handles reloads

### Dependencies

- **chokidar**: File system watcher for detecting changes
- **express**: Web server framework
- **socket.io**: Real-time bidirectional communication
- **fs, path, http, https**: Node.js built-in modules

## Installation

### Prerequisites

Ensure you have Node.js installed and the required dependencies:

```bash
npm install chokidar express socket.io
```

### SSL Certificates (Optional)

For HTTPS support, place SSL certificates in:
```
../dev/ssl/
├── sonub-key.pem
└── sonub-cert.pem
```

## Configuration

### Environment Variables

The server can be configured using environment variables:

| Variable | Default | Description |
|----------|---------|-------------|
| `DOMAIN` | `local.sonub.com` | Domain for the hot reload server |
| `PORT` | `3034` | Port for the WebSocket server |
| `USE_HTTPS` | `true` | Enable/disable HTTPS support |
| `KEY` | `/../dev/ssl/sonub-key.pem` | Path to SSL private key |
| `CERT` | `/../dev/ssl/sonub-cert.pem` | Path to SSL certificate |

### Watched Directories

The system monitors these directories by default:

- `./api` - API endpoints
- `./css` - Stylesheets
- `./etc` - Configuration files
- `./js` - JavaScript files
- `./lib` - Library files
- `./page` - Page templates
- `./post` - Post-related files
- `./res` - Resources
- `./user` - User-related files
- `./widgets` - Widget components
- `./api.php` - Main API file
- `./boot.php` - Bootstrap file
- `./index.php` - Entry point

### Ignored Files

The following patterns are ignored:

- `.git/` directories
- `node_modules/` directories
- `vendor/` directories
- `storage/` directories
- `cache/` directories
- `.map` files

## Usage

### Starting the Server

```bash
node etc/php-hot-reload-server.js
```

The server will start and display:
- Protocol (HTTP/HTTPS)
- Domain and port
- Watched directories
- SSL certificate status

### Including the Client

Add the client script to your PHP pages:

```php
<?php include 'etc/php-hot-reload-client.php'; ?>
```

This should be included before the closing `</body>` tag.

### Client Requirements

Ensure Socket.IO client library is available:

```html
<script src="/js/socket.io/socket.io.min.js"></script>
```

## File Structure

```
project/
├── etc/
│   ├── php-hot-reload-server.js    # WebSocket server
│   └── php-hot-reload-client.php   # Browser client script
├── js/
│   └── socket.io/
│       └── socket.io.min.js        # Socket.IO client library
└── ../dev/ssl/                     # SSL certificates (optional)
    ├── sonub-key.pem
    └── sonub-cert.pem
```

## How It Works

### Server Side

1. **File Watching**: Chokidar monitors specified directories for changes
2. **Event Processing**: Differentiates between CSS and other file changes
3. **WebSocket Communication**: Sends appropriate signals to connected clients
4. **SSL Handling**: Automatically configures HTTPS if certificates are available

### Client Side

1. **Connection**: Establishes WebSocket connection to the server
2. **CSS Hot Swap**: Updates stylesheet links with cache-busting parameters
3. **Full Reload**: Triggers `location.reload()` for non-CSS changes
4. **Dynamic Host Detection**: Automatically determines the correct server URL

### Event Types

- **`css`**: Emitted when CSS files change, includes the changed file path
- **`reload`**: Emitted when non-CSS files change, triggers full page reload

## SSL Configuration

### Automatic SSL Detection

The server automatically:
1. Checks for SSL certificate files
2. Falls back to HTTP if certificates are missing
3. Logs the SSL status and expected certificate paths

### Certificate Paths

Default certificate locations:
- Private Key: `../dev/ssl/sonub-key.pem`
- Certificate: `../dev/ssl/sonub-cert.pem`

### Generating Certificates

For local development, you can use tools like:
- mkcert
- OpenSSL
- Local CA authorities

## Troubleshooting

### Common Issues

1. **Connection Failed**
   - Check if the server is running on the correct port
   - Verify SSL certificates if using HTTPS
   - Ensure firewall isn't blocking the port

2. **CSS Not Hot Swapping**
   - Verify CSS file paths match the watched directories
   - Check browser console for client-side errors
   - Ensure stylesheet links include proper file names

3. **SSL Certificate Errors**
   - Verify certificate files exist and are readable
   - Check certificate validity and domain matching
   - Consider falling back to HTTP for development

### Debug Information

The server logs:
- File change events with file paths
- WebSocket connections and disconnections
- SSL certificate loading status
- Emitted events (css/reload)

The client logs:
- Connection status
- Hot reload events
- CSS update attempts

## Development

### Customizing Watch Paths

Modify the `WATCH_PATHS` array in `php-hot-reload-server.js`:

```javascript
const WATCH_PATHS = [
    './custom-directory',
    './another-path',
    // Add your paths here
];
```

### Adjusting Debounce Timing

Change the debounce delay (default 200ms):

```javascript
debounced(() => {
    io.emit('reload');
}, 500); // Custom delay in milliseconds
```

### Adding File Type Support

Extend CSS hot swap to other file types:

```javascript
const HOT_SWAP_EXT = new Set(['.css', '.scss', '.less']);
function isHotSwapFile(file) {
    return HOT_SWAP_EXT.has(path.extname(file).toLowerCase());
}
```

### Client Customization

The client script can be modified to:
- Handle additional event types
- Customize reload behavior
- Add loading indicators
- Implement retry logic

---

This hot reload system enhances development workflow by providing instant feedback for code changes while maintaining the flexibility to work with both HTTP and HTTPS configurations.