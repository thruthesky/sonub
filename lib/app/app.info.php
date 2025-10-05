<?php

function app_version(): array
{
    return [
        'version' => APP_VERSION,
    ];
}


function app_error(): array
{
    return error('app-error', 'A simple API for the error test');
}
