<?php
include('./_include.php');

// GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['project'])) error('No project specified.');
    $result = execute_sql(
        'SELECT t.id, m.user_id FROM mzpc_team t
        JOIN mzpc_member m ON t.id = m.team_id
        WHERE project_id = ?',
        [$_GET['project']])->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_GROUP);
    json($result);
}

// everything else here is resricted to admin access
if (!$user_is_admin) unauthorized();

// POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['name'])) error('Name not specified.');
    if (!isset($_POST['project_id'])) error('Project id not specified.');
    execute_sql('INSERT INTO mzpc_team (name, project_id) VALUES (?, ?)', [$_POST['name'], $_POST['project_id']]);
}

// PUT
else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $put); // get the contents of the PUT request
    if (!isset($_GET['id'])) error('Team id not specified.');
    if (!isset($put['name'])) error('Name not specified.');
    execute_sql('UPDATE mzpc_team SET name = ? WHERE id = ?', [$put['name'], $_GET['id']]);
}

// DELETE
else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (!isset($_GET['id'])) error('Team id not specified.');
    execute_sql('DELETE FROM mzpc_team WHERE id = ?', [$_GET['id']]);
}

// catch-all for other request types
error('Unknown request');
