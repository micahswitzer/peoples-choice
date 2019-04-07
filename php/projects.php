<?php
include('./_include.php'); // this give us some nice features

// GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['section'])) error('No section specified.');
    $result = execute_sql('SELECT id, name, status_open as isOpen FROM mzpc_project WHERE section_id = ?', [$_GET['section']])->fetchAll();
    json($result);
}

require_authorized();
if (!$user_is_admin) unauthorized();

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $_PUT); // get the contents of the PUT request
    if (!isset($_GET['project'])) error('No project specified.');
    if (isset($_PUT['open']))
        execute_sql('UPDATE mzpc_project SET status_open = ? WHERE id = ?', [$_PUT['open'],$_GET['project']])
            or error('Couldn\'t update project.');
    die();
}

else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (!isset($_GET['project'])) error('No project specified.');
    execute_sql('DELETE FROM mzpc_project WHERE id = ?', [$_GET['project']]) or error('Couldn\'t delete project.');
    die();
}

else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_GET['section'])) error('No section specified.');
    if (!isset($_POST['name'])) error('No section specified.');
    execute_sql('INSERT INTO mzpc_project (name, section_id) VALUES (?, ?)', [$_POST['name'], $_GET['section']]);
    json(array('project_id' => latest_id()));
}

// catch-all for other request types
error('Unknown request');
