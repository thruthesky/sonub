<?php
const ROOT_DIR = __DIR__;
include_once ROOT_DIR . '/etc/includes.php';

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
    </script>




</head>

<body>


    <?php include ROOT_DIR . '/etc/firebase/firebase-setup.php'; ?>
    <script src="/js/vue.global.prod.js"></script>
    <?php include_once ROOT_DIR . '/etc/php-hot-reload-client.php'; ?>

    <?php include_page_css() ?>
    <?php include_page_js() ?>


    <!-- Header Navigation -->
    <header class="bg-dark text-white">
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid">
                <!-- 모바일: [S] 표시, 데스크톱: Sonub 표시 -->
                <a class="navbar-brand" href="/">
                    <span class="d-md-none">[S]</span>
                    <span class="d-none d-md-inline">Sonub</span>
                </a>

                <!-- Mobile: 채팅, 친구찾기, User icon and toggler on the right -->
                <div class="d-flex align-items-center ms-auto">
                    <!-- 모바일 전용: 채팅, 친구찾기 아이콘 -->
                    <div class="d-md-none d-flex align-items-center">
                        <a href="/chat" class="me-2">
                            <i class="bi bi-chat-dots sonub-nav-icon"></i>
                        </a>
                        <a href="/friends" class="me-2">
                            <i class="bi bi-people sonub-nav-icon"></i>
                        </a>
                    </div>

                    <div class="me-2">
                        <a href="<?= href()->user->profile ?>">
                            <i class="bi bi-person-circle sonub-nav-icon"></i>
                        </a>
                        <a href="<?= href()->user->login ?>">
                            <i class="bi bi-box-arrow-in-right sonub-nav-icon"></i>
                        </a>
                    </div>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="/">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/posts">Posts</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/users">Users</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/about">About</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <button class="nav-link" onclick="firebase.auth().signOut()" type="button">Sign out</button>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= href()->user->login ?>">Sign in</a>
                        </li>
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
                    <button class="nav-link" onclick="firebase.auth().signOut()">Logout</button>
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
            <aside class="col-12 col-md-3 col-lg-2 bg-light p-3 border-start">
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
                    <p class="mb-0">&copy; 2024 Sonub Application. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>




    <script src="/etc/frameworks/bootstrap/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>