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
    <header>
        <nav>
            <a href="/">Home</a> |
            <a href="/posts">Posts</a> |
            <a href="/users">Users</a> |
            <a href="/about">About</a> |

            <a href="<?= href()->user->login ?>">Sign in</a>
        </nav>
    </header>

    <section>

        <aside>
            <h2>Left Sidebar</h2>
            <p>This is the sidebar content.</p>
        </aside>

        <main>
            <h1>Welcome to Sonub</h1>
            <p>Sonub -- application is running!</p>
            <p>This is a sample application using Firebase Phone Authentication and a MySQL database.</p>
        </main>

        <aside>
            <h2>Right Sidebar</h2>
            <p>This is the sidebar content.</p>
        </aside>

    </section>
    <footer>
        <p>&copy; 2024 Sonub Application</p>
    </footer>


    <?php include etc_folder('firebase/firebase-setup') ?>
</body>

</html>