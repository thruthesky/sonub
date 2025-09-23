// server.js
const fs = require('fs');
const path = require('path');
const https = require('https'); // HTTPS 권장 (혼합콘텐츠 회피)
const express = require('express');
const { Server } = require('socket.io');
const chokidar = require('chokidar');

const DOMAIN = 'local.philgo.com'; // 실제 접속 도메인
const PORT = 3030;               // 브라우저가 접속할 소켓 포트

// GoGetSSL 인증서 파일 경로 (key와 cert 올바르게 매핑)
const key = fs.readFileSync(path.join(__dirname, `./etc/nginx/ssl/old-philgo-ssl/philgo.private.key`));  // Private Key
const cert = fs.readFileSync(path.join(__dirname, `./etc/nginx/ssl/old-philgo-ssl/philgo.cert.ca-bundle`)); // Certificate Bundle

const app = express();
const server = https.createServer({ key, cert }, app);

// 소켓 서버 (다른 오리진에서도 받도록 간단 설정)
const io = new Server(server, {
    cors: { origin: true, credentials: true },
});

// 상태 체크용
app.get('/health', (_, res) => res.send('ok'));

// 감시 대상(원하는 경로로 수정)
const WATCH_PATHS = [
    './api',
    './company',
    './css',
    './etc',
    './family-site',
    './js',
    './lib',
    './page',
    './post',
    './res',
    './user',
    './widgets',
    './api.php',
    './boot.php',
    './includes.php',
    './index.php',
    './page.header.php',
    './page.footer.php',
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
    console.log(`Hotreload socket on https://${DOMAIN}:${PORT} started\nOpen https://local.philgo.com:444 in your browser to start development.`);
});
