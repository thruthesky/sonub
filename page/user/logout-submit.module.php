<?php

if (is_logout_page()) {
    if (login() != null) {
        // If the user is logged in, clear the session cookie
        clear_session_cookie();
    }
}
