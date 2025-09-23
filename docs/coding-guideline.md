Sonub Coding Guideline
=======================

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

### Using macOS Container
The Sonub development environment uses macOS Container (Apple's native container runtime) to run Nginx and PHP-FPM services.

#### Prerequisites
- macOS 26 (Tahoe) or later
- macOS Container installed (`container` command available)
- Project located at `~/apps/sonub/`

#### Quick Start Commands
```bash
# Navigate to container directory
cd ~/apps/sonub/dev/container

# Start the system (Nginx + PHP-FPM)
./sonub.sh start

# Check status
./sonub.sh status

# View logs
./sonub.sh logs

# Stop the system
./sonub.sh stop

# Restart the system
./sonub.sh restart

# Reload Nginx configuration
./sonub.sh reload

# Open browser to view the site
./sonub.sh open
```

#### Default Configuration
- **Web Root**: `/Users/thruthesky/apps/sonub`
- **HTTP Port**: 127.0.0.1:12345
- **PHP Version**: 8.3-fpm-alpine
- **Nginx Version**: alpine (latest)
- **Network**: webnet (192.168.65.x subnet)

#### Key Features
- Automatic PHP-FPM IP detection and configuration
- Directory-based volume mounting (macOS Container requirement)
- Automatic Nginx configuration updates
- Health checks and status monitoring

#### Troubleshooting
- If PHP-FPM IP changes, the script automatically updates Nginx configuration
- Check logs with `./sonub.sh logs` for debugging
- Ensure containers are on the same network (`webnet`)
- PHP-FPM typically runs at 192.168.65.x:9000
