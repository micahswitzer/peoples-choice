<?php
include('./_include.php');

// GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['section'])) error('No section specified.');
    $result = execute_sql('SELECT first_name, last_name FROM mzpc_user WHERE section_id = ?', [$_GET['section']])->fetchAll();
    json($result);
}

// POST
else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['first_name'])) error('First name not specified.');
    if (!isset($_POST['last_name'])) error('Last name not specified.');
    if (!isset($_POST['pass'])) error('Password not specified.');
    if (!isset($_POST['student'])) error('Student value not specified.');
    if (!isset($_POST['admin'])) error('Admin value not specified.');
    if (!isset($_POST['section_id'])) error('Section id not specified.');
    execute_sql('INSERT INTO mzpc_user (first_name, last_name, pass, student, admin, section_id)
      VALUES (?, ?, ?, ?, ?, ?)',
      [$_POST['first_name'], $_POST['last_name'], $_POST['pass'], $_POST['student'], $_POST['admin'], $_POST['section_id']]);
}

// PUT
else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $put); // get the contents of the PUT request
    if (!isset($_GET['id'])) error('User id not specified.');
    if (!isset($put['name'])) error('Name not specified.');
    
    if ($put['admin'] == 1) {
      execute_sql('UPDATE mzpc_user SET first_name = ?, last_name = ?, pass = ?, student = ? admin = ?
        WHERE id = ?',
        [$put['first_name'], $put['last_name'], $put['pass'], $put['student'], $put['admin'], $_GET['id']]);
    } else {
      execute_sql('UPDATE mzpc_user SET first_name = ?, last_name = ?, pass = ?, student = ? admin = ?
        WHERE id = ?', //only can be the id of the user editing, not 100% sure how to do this
        [$put['first_name'], $put['last_name'], $put['pass'], $put['student'], $put['admin'], $_GET['id']]);
    }
}

// DELETE
else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (!isset($_GET['id'])) error('User id not specified.');
    execute_sql('DELETE FROM mzpc_user WHERE id = ?', [$_GET['id']]);
}

// catch-all for other request types
error('Unknown request');
