# Running the RECI Reflection Gallery

## Quick Start

The gallery needs to be served from a web server (not opened directly as a file) to load the content properly.

### Option 1: Python HTTP Server (Recommended for Development)

```bash
# Navigate to the project directory
cd /Users/olalekan/Projects/reci/reflection-gallery

# Start a local server on port 8000
python3 -m http.server 8000

# Open in browser
open http://localhost:8000
```

### Option 2: Node.js HTTP Server

```bash
# Install http-server globally (one time)
npm install -g http-server

# Navigate to project directory
cd /Users/olalekan/Projects/reci/reflection-gallery

# Start server
http-server -p 8000

# Open in browser
open http://localhost:8000
```

### Option 3: PHP Built-in Server

```bash
# Navigate to project directory
cd /Users/olalekan/Projects/reci/reflection-gallery

# Start PHP server
php -S localhost:8000

# Open in browser
open http://localhost:8000
```

### Option 4: VS Code Live Server Extension

1. Install "Live Server" extension in VS Code
2. Right-click on `index.html`
3. Select "Open with Live Server"

## Why a Server is Needed

Modern browsers block JavaScript from loading local files (like `data/content.json`) when opening HTML files directly using the `file://` protocol. This is a security feature called CORS (Cross-Origin Resource Sharing).

When you open `index.html` directly, you'll see fallback sample data instead of the actual content.

## For Production Deployment

Upload all files to your web server (Apache, Nginx, etc.) and access via HTTP/HTTPS.

## Current Server

A Python HTTP server is currently running on **http://localhost:8000**

To stop it, press `Ctrl+C` in the terminal.
