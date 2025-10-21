<?php


if (login()) {
    include __DIR__ . '/index.my-page.php';
} else {
    include __DIR__ . '/index.guest.php';
}
