<?php
include('./_include.php');

// GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['section'])) error('No section specified.');
    $result = execute_sql('SELECT first_name, last_name, student as is_student, admin as is_admin FROM mzpc_user WHERE section_id = ?', [$_GET['section']])->fetchAll();
    json($result);
}

// users can only update if they are trying to update themselves, or they are an admin
$edit_self = $_GET['id'] == $user_id;
if (!($user_is_admin or $edit_self)) unauthorized();

// PUT
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $put); // get the contents of the PUT request
    if (!isset($_GET['id'])) error('User id not specified.');
    
    // update the password separately if it's present
    if (isset($put['pass'])) {
        execute_sql('UPDATE mzpc_user SET pass = ? WHERE id = ?', [password_hash($put['pass'], PASSWORD_DEFAULT), $_GET['id']]);
        if (!isset($put['first_name']) or !isset($put['last_name']))
            die(/*peacefully iff we've already updated the password*/);
    }
    if (!isset($put['first_name'])) error('First name not specified.');
    if (!isset($put['last_name'])) error('Last name not specified.');

    if ($edit_self) {
        execute_sql(
            'UPDATE mzpc_user SET first_name = ?, last_name = ? WHERE id = ?',
            [$put['first_name'], $put['last_name'], $_GET['id']]);
    }
    else {
        // only admins who are not editing themselves can change these values
        if (!isset($put['student'])) error('Student value not specified.');
        if (!isset($put['admin'])) error('Admin value not specified.');
        if (!isset($put['section_id'])) error('Section id not specified.');
        execute_sql(
            'UPDATE mzpc_user SET first_name = ?, last_name = ?, section_id = ?, student = ?, admin = ? WHERE id = ?',
            [$put['first_name'], $put['last_name'], $put['section_id'], $put['student'], $put['admin'], $_GET['id']]);
    }
    die(); // we're done here
}

// everything else here is resricted to admin access
if (!$user_is_admin) unauthorized();

// POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['first_name'])) error('First name not specified.');
    if (!isset($_POST['last_name'])) error('Last name not specified.');
    // no password specified when creating, default is 'password'
    if (!isset($_POST['student'])) error('Student value not specified.');
    if (!isset($_POST['admin'])) error('Admin value not specified.');
    if (!isset($_POST['section_id'])) error('Section id not specified.');
    execute_sql(
        'INSERT INTO mzpc_user (first_name, last_name, pass, student, admin, section_id) VALUES (?, ?, ?, ?, ?, ?)',
        [$_POST['first_name'], $_POST['last_name'], password_hash('password', PASSWORD_DEFAULT), $_POST['student'], $_POST['admin'], $_POST['section_id']]);
}

// DELETE
else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (!isset($_GET['id'])) error('User id not specified.');
    execute_sql('DELETE FROM mzpc_user WHERE id = ?', [$_GET['id']]);
}

// catch-all for other request types
error('Unknown request');
