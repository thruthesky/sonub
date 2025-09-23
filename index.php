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
    <?php include etc_folder('php-hot-reload-client') ?>
</head>

<body>
    <h1>Welcome to Sonub</h1>
    <p>Sonub -- application is running!</p>

    upated



    <?php include etc_folder('firebase/firebase-setup') ?>
</body>

</html>