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
        .sonub-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        /* Fixed header */
        .top-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1020;
        }

        .top-bar .navbar {
            padding-top: 8px;
            padding-bottom: 8px;
        }

        /* Body with light gray background */
        body {
            padding-top: 80px;
            background-color: #f0f2f5;
        }

        /* Logo */
        .navbar-logo {
            width: 40px;
            height: 40px;
            object-fit: cover;
        }

        /* Search component responsive width */
        @media (min-width: 768px) {
            .user-search-component {
                width: 240px !important;
            }
        }
    </style>

</head>

<body page="<?= htmlspecialchars(page(), ENT_QUOTES) ?>">



    <header id="page-header" class="top-bar bg-white border-bottom shadow-sm">
        <nav class="navbar navbar-light">
            <div class="container-fluid px-3">
                <div class="d-flex align-items-center justify-content-between w-100">

                    <!-- Left: Logo and Search -->
                    <div class="d-flex align-items-center ">
                        <a class="navbar-brand mb-0 p-0" href="/">
                            <img src="/res/img/logo/medium.png" class="navbar-logo rounded-circle" alt="Sonub Logo">
                        </a>
                        <!-- Search: auto width on mobile, 240px on desktop -->
                        <div class="user-search-component"></div>
                    </div>

                    <!-- Right: Icon Menu (Facebook-style circular buttons) -->
                    <div class="d-flex align-items-center gap-2">
                        <!-- Posts -->
                        <a href="<?= href()->post->categories ?>"
                            class="btn btn-light rounded-circle p-0 d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;"
                            title="Posts">
                            <i class="bi bi-grid fs-5"></i>
                        </a>

                        <!-- Users -->
                        <a href="<?= href()->user->list ?>"
                            class="btn btn-light rounded-circle p-0 d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;"
                            title="Users">
                            <i class="bi bi-people fs-5"></i>
                        </a>

                        <!-- Chat -->
                        <a href="<?= href()->chat->rooms ?>"
                            class="btn btn-light rounded-circle p-0 d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;"
                            title="Chat">
                            <i class="bi bi-chat-dots fs-5"></i>
                        </a>

                        <!-- Profile / Login -->
                        <?php if (login()) : ?>
                            <a href="<?= href()->user->profile ?>"
                                class="d-flex align-items-center justify-content-center rounded-circle overflow-hidden"
                                style="width: 40px; height: 40px;"
                                title="Profile">
                                <?php login_user_profile_photo() ?>
                            </a>
                        <?php else: ?>
                            <a href="<?= href()->user->login ?>"
                                class="btn btn-light rounded-circle p-0 d-flex align-items-center justify-content-center"
                                style="width: 40px; height: 40px;"
                                title="Login">
                                <i class="bi bi-person-circle fs-5"></i>
                            </a>
                        <?php endif; ?>

                        <!-- Menu -->
                        <a href="<?= href()->menu->intro ?>"
                            class="btn btn-light rounded-circle p-0 d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;"
                            title="Menu">
                            <i class="bi bi-list fs-5"></i>
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
                <?php include_once WIDGET_DIR . '/sidebar/quick-links.php'; ?>
            </aside>

            <!-- Main Content -->
            <main class="col-12 col-md-8 col-lg-6">
                <?php include page() ?>
            </main>

            <!-- Right Sidebar -->
            <aside class="sticky-sidebar d-none d-lg-flex flex-column gap-4 col-12 col-md-3 col-lg-3">
                <?php include_once WIDGET_DIR . '/sidebar/latest-posts.php'; ?>
                <?php include_once WIDGET_DIR . '/sidebar/new-users.php'; ?>
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
            top: 80px;
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