<?php


function etc(string $path): string
{
    return ROOT_DIR . '/etc/' . $path . '.php';
}
