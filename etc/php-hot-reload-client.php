<!-- socket.io-client (CDN) -->
<script src="/js/socket.io/socket.io.min.js"></script>
<script>
    (() => {
        // Dynamically determine the hot-reload server URL based on current hostname
        // If accessing via local.sonub.com, connect to local.sonub.com:3034
        // Otherwise, connect to localhost:3034
        const currentHost = window.location.hostname;

        // If accessing via any *.sonub.com subdomain, use that same host
        const hotReloadHost = currentHost.endsWith('.sonub.com') ? currentHost : 'localhost';
        const hotReloadUrl = `https://${hotReloadHost}:3034`;

        console.log('[hotreload] Connecting to:', hotReloadUrl);

        const socket = io(hotReloadUrl, {
            transports: ['websocket'], // Fast connection
            withCredentials: true
        });


        socket.on('connect', () => console.log('[hotreload] connected', socket.id));
        socket.on('connect_error', e => console.warn('[hotreload] connect_error', e.message));

        // ✅ CSS만 갱신 (전체 리로드 없음)
        socket.on('css', ({
            file
        }) => {
            console.log('[hotreload] css:', file);
            // 1) 바뀐 파일만 찾아 갱신 (링크 href안에 파일명이 포함되어 있으면)
            let matched = false;
            document.querySelectorAll('link[rel="stylesheet"]').forEach(link => {
                const href = link.getAttribute('href') || '';
                if (href.includes(file.split('/').pop())) {
                    const url = new URL(link.href, location.origin);
                    url.searchParams.set('v', Date.now().toString()); // 캐시버스트
                    link.href = url.toString();
                    matched = true;
                }
            });
            // 2) 매칭 실패하면 안전하게 전체 CSS 리프레시
            if (!matched) {
                document.querySelectorAll('link[rel="stylesheet"]').forEach(link => {
                    const url = new URL(link.href, location.origin);
                    url.searchParams.set('v', Date.now().toString());
                    link.href = url.toString();
                });
            }
        });

        // 🔄 그 외 변경(PHP/뷰/JS): 전체 새로고침
        socket.on('reload', () => {
            console.log('[hotreload] reload');
            location.reload();
        });
    })();
</script>