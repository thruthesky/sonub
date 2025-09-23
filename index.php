<?php
const ROOT_DIR = __DIR__;
include ROOT_DIR . '/etc/boot/boot.functions.php';
include etc_folder('includes');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sonub Application</title>
    <link href="/etc/frameworks/bootstrap/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        /** Register function to execute when DOMContentLoaded event occurs. Since jQuery is loaded with defer, you can use jQuery with this code pattern. */
        function ready(fn) {
            document.readyState !== "loading" ? fn() :
                document.addEventListener("DOMContentLoaded", fn);
        }
    </script>
    <script defer src="/js/alpinejs-3.15.0.min.js"></script>
    <script defer src="/js/jquery-4.0.0-rc.1.min.js"></script>
    <script defer src="/js/app.js"></script>
    <script defer src="/etc/frameworks/bootstrap/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <?php include etc_folder('php-hot-reload-client') ?>
</head>

<body>
    <!-- Header Navigation -->
    <header class="bg-dark text-white">
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="/">Sonub</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
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
                            <a class="nav-link" href="<?= href()->user->login ?>">Sign in</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content Area with Sidebars -->
    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Left Sidebar -->
            <aside class="col-12 col-md-3 col-lg-2 bg-light p-3 border-end">
                <h5 class="mb-3">Left Sidebar</h5>
                <nav class="nav flex-column">
                    <a class="nav-link" href="#">Dashboard</a>
                    <a class="nav-link" href="#">Profile</a>
                    <a class="nav-link" href="#">Settings</a>
                    <a class="nav-link" href="#">Messages</a>
                </nav>
                <hr>
                <div class="mt-3">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-decoration-none">Documentation</a></li>
                        <li><a href="#" class="text-decoration-none">Support</a></li>
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


    <?php include etc_folder('firebase/firebase-setup') ?>
</body>

</html>