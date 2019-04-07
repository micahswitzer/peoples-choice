<?php
include('./_include.php');

// GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['project'])) error('Project not specified.');
    json(execute_sql(
        'SELECT w.team_id, w.message FROM mzpc_write_in w
        JOIN mzpc_team t ON w.team_id = t.id
        WHERE t.project_id = ?',
        [$_GET['project']])->fetchAll());
}

require_authorized();

// POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['message'])) error('Message not specified.');
    if (!isset($_POST['team_id'])) error('Team id not specified.');
    execute_sql('INSERT INTO mzpc_write_in (message, user_id, team_id) VALUES (?, ?, ?)', [$_POST['message'], $user_id, $_POST['team_id']]);
    die();
}

// catch-all for other request types
error('Unknown request');
