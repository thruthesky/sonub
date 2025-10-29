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
            if (document.readyState !== "loading") {
                initialize_on_ready();
                fn();
                return;
            }
            document.addEventListener("DOMContentLoaded", () => {
                initialize_on_ready();
                fn();
            });
        }


        // Firebase 초기화 함수. 여러번 호출해도 한번만 초기화 함. 그리고 </body> 태그 바로 직전에서 한번 호출 됨
        function firebase_ready(callback) {
            ready(() => {
                if (typeof firebase === 'undefined') {
                    throw new Error("No firebase. Firebase SDK script not loaded.");
                }
                if (firebase.apps.length > 0) {
                    // 이미 초기화 되었으면 바로 콜백 호출
                    // console.log("---> Firebase 이미 초기화 되어 있음");
                    callback();
                    return;
                }
                // 파이어베이스 app 초기화: Firebase (and user) available immediately after this line.
                firebase.initializeApp({
                    apiKey: "AIzaSyCXAFYnNf7QYcZNpIngCA-lOhb9YRRLDTY",
                    authDomain: "sonub-firebase.firebaseapp.com",
                    projectId: "sonub-firebase",
                    storageBucket: "sonub-firebase.firebasestorage.app",
                    messagingSenderId: "406320255657",
                    appId: "1:406320255657:web:79b39117a353878b8e8fb8",
                    measurementId: "G-3XX2YB4JV0"
                });
                console.log("---> Firebase is initialized now.");
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



        // // Store (Vue 3 전역 상태 관리)
        // ready(() => {
        //     // Vue 전역 스토어 (모든 앱이 공유)

        //     const {
        //         reactive,
        //         computed
        //     } = Vue;

        //     // 1️⃣ 상태 (state)
        //     const state = reactive({
        //         user: window.__HYDRATE__?.user ?? null, // index.php에서 주입된 로그인 사용자 정보
        //         lang: window.__HYDRATE__?.lang || 'en' // index.php에서 주입된 사용자 언어 정보
        //     });

        //     // 2️⃣ 계산값 (getters)
        //     const getters = {
        //         doubled: computed(() => state.count * 2)
        //     };

        //     // 3️⃣ 액션 (actions)
        //     const actions = {
        //         setUser(u) {
        //             state.user = u;
        //         },
        //         setUserPhotoUrl(url) {
        //             state.user = {
        //                 ...state.user,
        //                 photo_url: url
        //             };
        //         }
        //     };

        //     // 4️⃣ 전역 노출 (모든 Vue 앱이 동일한 인스턴스를 사용)
        //     window.Store = {
        //         state,
        //         getters,
        //         actions
        //     };

        //     console.log('window.Store is loaded');
        // });
    </script>


    <style>
        /* Sonub 컨테이너 - 콘텐츠 최대 너비 제한 */
        .sonub-container {
            max-width: 1200px;
            /* 최대 너비를 1200px로 제한 */
            margin: 0 auto;
            /* 가운데 정렬 */
            padding: 0 15px;
            /* 좌우 여백 15px */
        }

        /* Header Top Bar 스타일 - Fixed 고정 */
        .top-bar {
            position: fixed;
            /* 항상 상단에 고정 (스크롤과 무관) */
            top: 0;
            /* 페이지 상단에 고정 */
            left: 0;
            /* 왼쪽 정렬 */
            right: 0;
            /* 오른쪽 정렬 (전체 너비) */
            z-index: 1020;
            /* Bootstrap의 다른 요소들보다 위에 표시 */
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            /* 하단 그림자 효과 */
        }

        /* Top Bar 내부 navbar의 padding 최소화 */
        .top-bar .navbar {
            padding-top: 4px;
            /* 상단 패딩 4px */
            padding-bottom: 4px;
            /* 하단 패딩 4px */
        }

        /* Body에 헤더 높이만큼 padding-top 추가 (콘텐츠가 헤더에 가려지지 않도록) */
        body {
            padding-top: 80px;
            /* 헤더 높이만큼 여백 추가 */
        }

        @media (min-width: 768px) {
            body {
                padding-top: 88px;
                /* 모바일에서는 헤더 높이 증가에 따라 여백 증가 */
            }
        }

        /* 네비게이션 로고 이미지 스타일 */
        .navbar-logo {
            width: 40px;
            /* 로고 너비 */
            height: 40px;
            /* 로고 높이 */
            object-fit: cover;
            /* 이미지 비율 유지하면서 영역 채우기 */
        }

        /* 네비게이션 링크 호버 효과 */
        .navbar-light .navbar-nav .nav-link:hover {
            color: var(--bs-primary) !important;
            /* 호버 시 primary 색상 */
            transition: color 0.2s ease;
            /* 부드러운 색상 전환 */
        }

        /* 아이콘 링크 호버 효과 */
        .top-bar a:hover i {
            color: var(--bs-primary) !important;
            /* 호버 시 primary 색상 */
            transition: color 0.2s ease;
            /* 부드러운 색상 전환 */
        }
    </style>

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
                    <div class="d-flex align-items-center gap-3">

                        <!-- 게시판 카테고리 아이콘 -->
                        <a href="<?= href()->post->categories ?>" class="text-dark" title="Posts">
                            <i class="bi bi-grid fs-3"></i>
                        </a>

                        <!-- 사용자 목록 아이콘 -->
                        <a href="<?= href()->user->list ?>" class="text-dark" title="Users">
                            <i class="bi bi-people fs-3"></i>
                        </a>

                        <!-- 채팅 아이콘 -->
                        <a href="<?= href()->chat->rooms ?>" class="text-dark" title="Chat">
                            <i class="bi bi-chat-dots fs-3"></i>
                        </a>

                        <!-- 로그인 상태에 따른 아이콘 -->
                        <!-- Temporarily change to icon instead of profile picture -->
                        <?php if (login()) : ?>
                            <a href="<?= href()->user->profile ?>" class="text-dark" title="Profile">
                                <?php login_user_profile_photo() ?>
                            </a>
                        <?php else: ?>
                            <a href="<?= href()->user->login ?>" class="text-dark" title="Login">
                                <i class="bi bi-person-circle fs-3"></i>
                            </a>
                        <?php endif; ?>

                        <!-- 메뉴 아이콘 (로그아웃 대신) -->
                        <a href="<?= href()->menu->intro ?>" class="text-dark" title="Menu">
                            <i class="bi bi-list fs-3"></i>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content Area with Sidebars -->
    <div class="sonub-container">
        <div class="row">
            <!-- Left Sidebar -->
            <aside class="sticky-sidebar d-none d-md-flex d-lg-flex flex-column gap-4 col-12 col-md-4 col-lg-3">
                <?php include_once WIDGET_DIR . '/sidebar/quick-user-menu.php'; ?>
                <?php include_once WIDGET_DIR . '/sidebar/new-users.php'; ?>
                <?php include_once WIDGET_DIR . '/sidebar/quick-links.php'; ?>
            </aside>

            <!-- Main Content -->
            <main class="col-12 col-md-8 col-lg-6">
                <?php include page() ?>
            </main>

            <!-- Right Sidebar -->
            <aside class="sticky-sidebar d-none d-lg-flex flex-column gap-4 col-12 col-md-3 col-lg-3">
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
                <?php if (is_admin()): ?>
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="app-version text-center">
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
                <?php endif; ?>
            </div>

        </footer>
    <?php else: ?>
        <footer id="page-footer" class="page-footer-placeholder"></footer>
    <?php endif; ?>






    <?php if (is_dev_computer()) {
        include ROOT_DIR . '/etc/dev/dev-footer.php';
    } ?>


    <script src="/js/vue.global.prod.js"></script>
    <script src="/js/axios.js"></script>
    <?php include_once ROOT_DIR . '/etc/php-hot-reload-client.php'; ?>
    <link href="/css/app.css?v=<?= APP_VERSION ?>" rel="stylesheet">
    <script src="/js/app.js?v=<?= APP_VERSION ?>" defer></script>

    <style>
        /* Sticky sidebars */
        .sticky-sidebar {
            position: sticky;
            top: 100px;
            /* Offset for fixed header (80-88px) + spacing */
            align-self: flex-start;
            /* Prevent stretching */
            max-height: calc(100vh - 120px);
            /* Prevent sidebar from being taller than viewport */
            overflow-y: auto;
            /* Allow scrolling if sidebar content is too long */
        }
    </style>



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
        firebase_ready(() => {
            firebase.auth().onAuthStateChanged((user) => {
                user.getIdToken().then((idToken) => {
                    console.log('ID Token:', idToken);
                })
            });
        })
    </script>

    <script>
        __HYDRATE__ = {};
        __HYDRATE__.user = <?php echo login() ? json_encode(login()->data(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : 'null'; ?>;
        __HYDRATE__.lang = "<?php echo get_user_lang(); ?>";
    </script>

    <script src="/js/petite-vue.iife.js" defer></script>
    <script>
        ready(() => {
            // v-scope 가 붙은 요소만 골라서 Petite Vue 적용
            document.querySelectorAll('[v-scope]').forEach(el => {
                // 각 v-scope 엘리먼트를 독립적으로 마운트
                PetiteVue.createApp().mount(el)
            })
        })
    </script>

    <script defer src="https://www.gstatic.com/firebasejs/12.3.0/firebase-app-compat.js"></script>
    <script defer src="https://www.gstatic.com/firebasejs/12.3.0/firebase-auth-compat.js"></script>
    <script defer src="https://www.gstatic.com/firebasejs/12.3.0/firebase-messaging-compat.js"></script>
    <script defer src="https://www.gstatic.com/firebasejs/12.3.0/firebase-database-compat.js"></script>
</body>

</html>