<?php
include('./_include.php');\

// GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['project_id'])) error('Project id not specified.');
    json(execute_sql(
        'SELECT w.message, w.team_id FROM mzpc_write_in w
        JOIN mzpc_team t ON w.team_id = t.id
        WHERE t.project_id = ?',
        [$_GET['project_id']])->fetchAll());
}

// POST
else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['user_id'])) error('User id not specified.');
    execute_sql('INSERT INTO mzpc_write_in (message, user_id, team_id) VALUES (?, ?, ?)', [$_POST['message'], $user_id, $_POST['team_id']]);
    die();
}

// catch-all for other request types
error('Unknown request');
