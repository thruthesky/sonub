// server.js
const fs = require('fs');
const path = require('path');
const http = require('http');
const https = require('https');
const express = require('express');
const { Server } = require('socket.io');
const chokidar = require('chokidar');

// Configuration
const DOMAIN = process.env.DOMAIN || 'local.sonub.com'; // Default to local.sonub.com
const PORT = process.env.PORT || 3034;                  // Socket server port
const USE_HTTPS = process.env.USE_HTTPS !== 'false';    // Default to HTTPS

const app = express();

// Create server with or without SSL based on configuration
let server;
if (USE_HTTPS) {
    try {
        // Use only local.sonub.com certificates
        const keyPath = path.join(__dirname, '../etc/server-settings/nginx/ssl/sonub/local.sonub.com-key.pem');
        const certPath = path.join(__dirname, '../etc/server-settings/nginx/ssl/sonub/local.sonub.com-cert.pem');

        if (fs.existsSync(keyPath) && fs.existsSync(certPath)) {
            const key = fs.readFileSync(keyPath);
            const cert = fs.readFileSync(certPath);
            server = https.createServer({ key, cert }, app);
            console.log('✅ HTTPS server configured with local.sonub.com SSL certificates');
        } else {
            console.log('⚠️ local.sonub.com SSL certificates not found, falling back to HTTP');
            console.log('   Expected paths:');
            console.log(`   - ${keyPath}`);
            console.log(`   - ${certPath}`);
            server = http.createServer(app);
        }
    } catch (error) {
        console.error('Error loading SSL certificates:', error.message);
        console.log('⚠️ Falling back to HTTP server');
        server = http.createServer(app);
    }
} else {
    server = http.createServer(app);
    console.log('Running in HTTP mode (USE_HTTPS=false)');
}

// 소켓 서버 (다른 오리진에서도 받도록 간단 설정)
const io = new Server(server, {
    cors: { origin: true, credentials: true },
});

// 상태 체크용
app.get('/health', (_, res) => res.send('ok'));

// 감시 대상(원하는 경로로 수정)
const WATCH_PATHS = [
    './api',
    './css',
    './etc',
    './js',
    './lib',
    './page',
    './post',
    './res',
    './user',
    './widgets',
    './api.php',
    './boot.php',
    './index.php',
];

// 무시 목록/디바운스
const IGNORED = [
    '**/.git/**', '**/node_modules/**', '**/vendor/**',
    '**/storage/**', '**/cache/**', '**/*.map',
];

let timer = null;
function debounced(fn, delay = 200) {
    if (timer) clearTimeout(timer);
    timer = setTimeout(fn, delay);
}

const CSS_EXT = new Set(['.css']); // 확장 시 필요하면 scss/sass 빌드 산출물만 감시
function isCssFile(file) {
    return CSS_EXT.has(path.extname(file).toLowerCase());
}

chokidar.watch(WATCH_PATHS, {
    ignoreInitial: true,
    ignored: IGNORED,
}).on('all', (event, file) => {
    // 변경된 파일 경로 로그
    console.log(`${event} ${file}`);

    // CSS만 바뀐 경우: CSS 핫스왑 신호
    if (isCssFile(file)) {
        debounced(() => {
            io.emit('css', { file });
            console.log('> emit css');
        });
        return;
    }

    // 그 외(PHP/템플릿/JS 등): 전체 리로드
    debounced(() => {
        io.emit('reload');
        console.log('> emit reload');
    });
});

server.listen(PORT, () => {
    const protocol = server instanceof https.Server ? 'https' : 'http';
    console.log(`\n🚀 Hot-reload server started`);
    console.log(`   Protocol: ${protocol}`);
    console.log(`   Domain: ${DOMAIN}`);
    console.log(`   Port: ${PORT}`);
    console.log(`   URL: ${protocol}://${DOMAIN}:${PORT}`);
    console.log(`\n📁 Watching for changes in:`);
    WATCH_PATHS.forEach(p => console.log(`   • ${p}`));
    console.log(`\n💡 To use HTTPS, ensure SSL certificates exist in:`);
    console.log(`   ./etc/server-settings/nginx/ssl/sonub/`);
    console.log(`   - local.sonub.com-key.pem`);
    console.log(`   - local.sonub.com-cert.pem`);
    console.log(`\n🌐 Open your development site and the hot-reload will work automatically.\n`);
});
