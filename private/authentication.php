<?php

// AUTHENTICATION FUNCTIONS //

function log_out() {
    unset($_SESSION['username']);
    unset($_SESSION['last_login']);
    unset($_SESSION['login_expires']);

    $_SESSION = array();

    session_destroy();

    header('Location: index.php');
    exit();
}

// Returns TRUE if the last login time plus the allowed time is still greater than the current time.
function last_login_is_recent() {
    // 60 seconds times 60 minutes is one hour.
    $max_time_elapsed = 60 * 60;

    // Is this $_SESSION value set? If not, they're not logged in.
    if (!isset($_SESSION['last_login'])) {
        return FALSE;
    }

    // If it is set, check to see if the time of the last login plus the maximum time allowed is greater than the current time. 
    return ($_SESSION['last_login'] + $max_time_elapsed) >= time();
}

function login_is_still_valid() {
    if (!isset($_SESSION['login_expires'])) {
        return FALSE;
    }

    return ($_SESSION['login_expires']) >= time();
}

?>