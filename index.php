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


    <!-- Google Fonts - Noto Sans KR -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">



    <!-- 기본 파비콘 -->
    <link rel="icon" type="image/png" sizes="32x32" href="/res/favicons/favicon-300.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/res/favicons/favicon-300.png">

    <!-- 애플 터치 아이콘 -->
    <link rel="apple-touch-icon" sizes="180x180" href="/res/favicons/apple-touch-icon.png">

    <!-- 안드로이드/크롬 -->
    <link rel="icon" type="image/png" sizes="192x192" href="/res/favicons/android-chrome-300.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/res/favicons/android-chrome-300.png">


    <script>
        /** DOMContentLoaded 이벤트가 발생했을 때 실행할 함수 등록. jQuery 가 defer 로드되므로, 이 코드를 활용해서 jQuery 를 쓰면 된다. */
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


    <script>
        const appConfig = <?php echo json_encode(config(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    </script>


</head>

<body>



    <!-- Header Navigation -->
    <header class="top-bar bg-white border-bottom">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <!-- 로고: Bootstrap Icons bi-chat-heart-fill -->
                <a class="navbar-brand d-flex align-items-center" href="/">
                    <i class="bi bi-chat-heart-fill text-dark fs-4 me-2"></i>
                    <span class="text-dark d-none d-md-inline">Sonub</span>
                </a>

                <!-- 우측 아이콘 및 토글 버튼 -->
                <div class="d-flex align-items-center ms-auto">
                    <!-- 모바일 전용: 채팅, 친구찾기 아이콘 -->
                    <div class="d-md-none d-flex align-items-center">
                        <a href="<?= href()->friend->find_friend ?>" class="me-3 text-dark">
                            <i class="bi bi-people fs-5"></i>
                        </a>


                        <a href="/chat" class="me-3 text-dark">
                            <i class="bi bi-chat-dots fs-5"></i>
                        </a>
                    </div>

                    <!-- 프로필 아이콘 -->
                    <div class="me-3">
                        <a href="<?= href()->user->profile ?>" class="text-dark">
                            <i class="bi bi-person-circle fs-5"></i>
                        </a>
                    </div>

                    <!-- 모바일 토글 버튼 -->
                    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>

                <!-- 네비게이션 메뉴 -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link text-dark" href="/">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark" href="/posts">Posts</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark" href="/users">Users</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark" href="/about">About</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav">
                        <?php if (login() == null) { ?>
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="<?= href()->user->login ?>">Sign in</a>
                            </li>
                        <?php } else { ?>
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="<?= href()->user->logout_submit ?>">Sign out</a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content Area with Sidebars -->
    <div class="sonub-container mt-4">
        <div class="row">
            <!-- Left Sidebar -->
            <aside class="d-none d-lg-block col-12 col-md-3 col-lg-2 bg-light p-3 border-end">
                <h5 class="mb-3">Left Sidebar</h5>
                <nav class="nav flex-column">
                    <a class="nav-link" href="<?= href()->user->login ?>">Login</a>
                    <a class="nav-link" href="#">Dashboard</a>
                    <a class="nav-link" href="<?= href()->user->profile ?>">Profile</a>
                    <a class="nav-link" href="#">Settings</a>
                    <a class="nav-link" href="#">Messages</a>
                </nav>
                <hr>
                <div class="mt-3">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-decoration-none">Documentation</a></li>
                        <li><a href="/support" class="text-decoration-none">Support</a></li>
                        <li><a href="#" class="text-decoration-none">Contact</a></li>
                    </ul>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="col-12 col-md-6 col-lg-8 p-4">
                <?php include page() ?>
            </main>

            <!-- Right Sidebar -->
            <aside class="d-none d-lg-block col-12 col-md-3 col-lg-2 bg-light p-3 border-start">
                <h5 class="mb-3">Right Sidebar</h5>
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="card-title">Recent Activity</h6>
                        <ul class="list-unstyled small">
                            <li>User joined - 5 mins ago</li>
                            <li>New post created - 1 hour ago</li>
                            <li>Comment added - 2 hours ago</li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Statistics</h6>
                        <dl class="row small">
                            <dt class="col-6">Users</dt>
                            <dd class="col-6">1,234</dd>
                            <dt class="col-6">Posts</dt>
                            <dd class="col-6">567</dd>
                            <dt class="col-6">Comments</dt>
                            <dd class="col-6">890</dd>
                        </dl>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white mt-5 py-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 text-center">
                    <!-- Language Selector -->
                    <div class="mb-2">
                        <?php include ROOT_DIR . '/widgets/language/language-selector.php'; ?>
                    </div>
                    <p class="mb-0">&copy; 2024 Sonub Application. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>






    <?php include ROOT_DIR . '/etc/firebase/firebase-setup.php'; ?>
    <script src="/js/axios.js"></script>
    <script src="/js/vue.global.prod.js"></script>
    <?php include_once ROOT_DIR . '/etc/php-hot-reload-client.php'; ?>
    <link href="/css/app.css" rel="stylesheet">
    <script src="/js/app.js" defer></script>
    <?php if (is_index_page()) { ?>
    <?php } else { ?>
        <?php include_page_css() ?>
        <?php include_page_js() ?>
    <?php } ?>


    <script src="/etc/frameworks/bootstrap/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>