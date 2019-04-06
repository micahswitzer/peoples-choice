<?php
include('./_include.php');

function sendUserId() {
    $result = [];
    if (isset($_SESSION['user_id'])) {
        $result['user_id'] = $_SESSION['user_id'];
    }
    else {
        $result['user_id'] = null;
    }
    json($result);
}

if (!isset($_GET['action'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        sendUserId();
    }

    // catch-all for other request types
    error('Unknown request');
}
else {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        // catch-all for other request types
        error('Unknown request');
    }

    $action = $_GET['action'];
    
    if ($action === 'login') {
        if (!isset($_POST['username'])) error('No username provided.');
        if (!isset($_POST['password'])) error('No password provided.');
        $potentialUser = execute_sql(
            'SELECT id, pass as password_hash, student as is_student, admin as is_admin FROM mzpc_user WHERE linux_name = ?',
            [$_POST['username']])->fetch();
        if ($potentialUser === null) error('Invalid credentials.');
        if (password_verify($_POST['password'], $potentialUser['password_hash'])) {
            $_SESSION['user_id'] = $potentialUser['id'];
            $_SESSION['user_is_student'] = $potentialUser['is_student'] == '1';
            $_SESSION['user_is_admin'] = $potentialUser['is_admin'] == '1';
            sendUserId();
        }
        else error('Invalid credentials.');
    }
    else if ($action === 'logout') {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_is_student']);
        unset($_SESSION['user_is_admin']);
        die();
    }

    // catch-all for other actions
    error("Unknown action '$action'");
}