<?php


include_once './init.php';


// 모듈 로드
$module_path = get_module_path();
if (file_exists($module_path)) {
    include $module_path;
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sonub Application</title>
    <link href="/etc/frameworks/bootstrap/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/etc/frameworks/bootstrap/bootstrap-icons-1.13.1/bootstrap-icons.css">
    <link rel="stylesheet" href="/etc/frameworks/fontawesome/css/all.min.css">


    <!-- Google Fonts - Noto Sans KR -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">



    <!-- 기본 파비콘 -->
    <link rel="icon" type="image/png" sizes="32x32" href="/res/img/logo/small.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/res/img/logo/small.png">

    <!-- 애플 터치 아이콘 -->
    <link rel="apple-touch-icon" sizes="180x180" href="/res/img/logo/medium.png">

    <!-- 안드로이드/크롬 -->
    <link rel="icon" type="image/png" sizes="192x192" href="/res/img/logo/medium.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/res/img/logo/medium.png">


    <script>
        /** DOMContentLoaded 이벤트가 발생했을 때 실행할 함수 등록. */
        function ready(fn) {
            document.readyState !== "loading" ? fn() :
                document.addEventListener("DOMContentLoaded", fn);
        }


        // Firebase 초기화 함수. 여러번 호출해도 한번만 초기화 함. 그리고 </body> 태그 바로 직전에서 한번 호출 됨
        function firebase_ready(callback) {
            ready(() => {
                if (typeof firebase === 'undefined') {
                    throw new Error("No firebase. Firebase SDK script not loaded.");
                }
                if (firebase.apps.length > 0) {
                    // 이미 초기화 되었으면 바로 콜백 호출
                    console.log("---> Firebase 이미 초기화 되어 있음");
                    callback();
                    return;
                }
                // 파이어베이스 app 초기화: Firebase (and user) available immediately after this line.
                firebase.initializeApp(firebaseConfig);
                console.log("---> Firebase 초기화 됨");
                callback();
            })
        }
    </script>


    <?php if (is_dev_computer()) { ?>
        <script src="/js/dev.js"></script>
    <?php } ?>


    <script>
        // 애플리케이션 전역 설정
        const appConfig = <?php echo json_encode(config()->toArray(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

        // 서버에서 클라이언트로 데이터 전달용 객체 (Hydration)
        window.__HYDRATE__ = {};
    </script>


</head>

<body page="<?= htmlspecialchars(page(), ENT_QUOTES) ?>">



    <!-- 헤더 : 탑바 -->
    <header id="page-header" class="top-bar bg-white border-bottom">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <!-- 로고: Sonub Logo Image -->
                <a class="navbar-brand d-flex align-items-center" href="/">
                    <img src="/res/img/logo/medium.png" class="navbar-logo rounded-circle me-2" alt="Sonub Logo">
                </a>

                <!-- 우측 아이콘 메뉴 (모바일 & 데스크톱 공통) -->
                <div class="d-flex align-items-center ms-auto">
                    <!-- 아이콘 메뉴 (모든 화면 크기에서 표시) -->
                    <div class="d-flex align-items-center">

                        <!-- 게시판 카테고리 아이콘 -->
                        <a href="<?= href()->post->categories ?>" class="me-3 text-dark" title="Posts">
                            <i class="bi bi-grid fs-4"></i>
                        </a>

                        <!-- 사용자 목록 아이콘 -->
                        <a href="<?= href()->user->list ?>" class="me-3 text-dark" title="Users">
                            <i class="bi bi-people fs-4"></i>
                        </a>

                        <!-- 채팅 아이콘 -->
                        <a href="<?= href()->chat->rooms ?>" class="me-3 text-dark" title="Chat">
                            <i class="bi bi-chat-dots fs-4"></i>
                        </a>

                        <!-- About 아이콘 -->
                        <a href="/about" class="me-3 text-dark" title="About">
                            <i class="bi bi-info-circle fs-4"></i>
                        </a>

                        <!-- 로그인 상태에 따른 아이콘 -->
                        <?php if (login() == null) { ?>
                            <!-- 로그인 아이콘 -->
                            <a href="<?= href()->menu->intro ?>" class="me-3 text-dark" title="Sign in">
                                <i class="bi bi-list fs-4"></i>
                            </a>
                        <?php } else { ?>
                            <!-- 프로필 아이콘 -->
                            <a href="<?= href()->user->profile ?>" class="me-3 text-dark" title="Profile">
                                <i class="user-profile-icon"></i>
                            </a>

                            <!-- 메뉴 아이콘 (로그아웃 대신) -->
                            <a href="<?= href()->menu->intro ?>" class="text-dark" title="Menu">
                                <i class="bi bi-list fs-4"></i>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content Area with Sidebars -->
    <div class="sonub-container">
        <div class="row">
            <!-- Left Sidebar -->
            <aside class="d-none d-lg-block col-12 col-md-3 col-lg-3 px-0">
                <?php include_once WIDGET_DIR . '/sidebar/new-users.php'; ?>
                <?php include_once WIDGET_DIR . '/sidebar/quick-links.php'; ?>
            </aside>

            <!-- Main Content -->
            <main class="col-12 col-md-6 col-lg-6">
                <?php include page() ?>
            </main>

            <!-- Right Sidebar -->
            <aside class="d-none d-lg-block col-12 col-md-3 col-lg-3">

                <?php include_once WIDGET_DIR . '/sidebar/latest-posts.php'; ?>
                <?php include_once WIDGET_DIR . '/sidebar/sidebar-stats.php'; ?>

            </aside>
        </div>
    </div>

    <!-- Footer -->


    <?php if (show_footer()) : ?>
        <footer id="page-footer" class="bg-light border-top mt-5 py-4">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        <a href="<?= href()->home ?>" class="text-primary text-decoration-none me-3 fw-semibold">Home</a>
                        <a href="<?= href()->help->guideline ?>" class="text-secondary text-decoration-none me-3">About</a>
                        <a href="<?= href()->admin->contact ?>" class="text-secondary text-decoration-none">Contact</a>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <!-- Language Selector -->
                        <div class="mb-2">
                            <?php include ROOT_DIR . '/widgets/language/language-selector.php'; ?>
                        </div>
                    </div>
                </div>
                <hr class="my-3 border-secondary border-opacity-25">
                <div class="row">
                    <div class="col-12 text-center">
                        <p class="mb-0 text-muted small">&copy; 2024 Sonub Application. All rights reserved.</p>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="app-version text-center">
                            <span class="version-label">Version</span>
                            <span class="version-number">
                                <?php
                                // APP_VERSION을 사람이 읽기 편한 형식으로 변환
                                // 형식: 2025-10-13-23-01-54 → 2025년 10월 13일 23:01:54
                                $version_parts = explode('-', APP_VERSION);
                                if (count($version_parts) === 6) {
                                    echo $version_parts[0] . '년 ' . $version_parts[1] . '월 ' . $version_parts[2] . '일 ' . $version_parts[3] . ':' . $version_parts[4] . ':' . $version_parts[5];
                                } else {
                                    echo APP_VERSION;
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </footer>
    <?php else: ?>
        <footer id="page-footer" class="page-footer-placeholder"></footer>
    <?php endif; ?>






    <?php if (is_dev_computer()) {
        include ROOT_DIR . '/etc/dev/dev-footer.php';
    } ?>


    <?php include ROOT_DIR . '/etc/firebase/firebase-setup.php'; ?>
    <script src="/js/axios.js"></script>
    <script src="/js/vue.global.prod.js"></script>
    <?php include_once ROOT_DIR . '/etc/php-hot-reload-client.php'; ?>
    <link href="/css/app.css?v=<?= APP_VERSION ?>" rel="stylesheet">
    <script src="/js/app.js" defer></script>



    <script src="/etc/frameworks/bootstrap/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

    <?php
    // ------------------------------------------------
    // Loading deferred JavaScripts in priority order
    // ------------------------------------------------
    foreach ($global_deferred_scripts as $priority => $scripts) {
        foreach ($scripts as $script) {
            echo "\n" . get_deferred_script_tag($script);
        }
    }
    ?>

    <script>
        __HYDRATE__.user = <?php echo login() ? json_encode(login()->data(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : 'null'; ?>;
    </script>

</body>

</html>