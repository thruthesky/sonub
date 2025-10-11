<?php

/**
 * @file lib/input.functions.php
 * @brief Input handling functions for the API.
 *
 * This file contains functions to handle input from the client, including merging
 * JSON input with POST/GET data.
 */


// Get JSON input
$__json         = @file_get_contents('php://input');
$__decoded_json = json_decode($__json, true);

// Merge POST/GET with JSON data, with proper null checking
$__in = [];
if ($__decoded_json !== null) {
    $__in = array_merge($_REQUEST, $__decoded_json);
} else {
    $__in = $_REQUEST;
}




/**
 * Returns the http input from client.
 * @return mixed
 * - the input parameter value as string
 * - or null if the value is one of 'null', 'undefined', empty string
 *   - but string of false, 0, will be returned as is
 */
function http_params(string $name = ''): mixed
{
    global $__in;

    if ($name === '') {
        return $__in;
    } else {
        if (isset($__in[$name])) {
            if ($__in[$name] === 'null' || $__in[$name] === 'undefined' || $__in[$name] === '') {
                return null;
            }
        }
        return $__in[$name] ?? null;
    }
}

function http_param(string $name = '', mixed $default_value = null): mixed
{
    return http_params($name) ?? $default_value;
}
