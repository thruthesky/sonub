<?php

inject_http_input('category', 'discussion');
inject_http_input('visibility', 'public');
include __DIR__ . '/post/list.php';
