Sonub Coding Guideline
=======================

## Table of Contents
- [General Coding Standards](#general-coding-standards)
- [Framework and Library Storage Guidelines](#framework-and-library-storage-guidelines)
  - [Complete Framework Packages](#complete-framework-packages)
  - [Single JavaScript Libraries](#single-javascript-libraries)
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