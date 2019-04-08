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
require_authorized();
if (!$user_is_admin) unauthorized();

// POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_GET['project'])) error('No project specified.');
    execute_sql('DELETE FROM mzpc_team WHERE project_id = ?', [$_GET['project']]) or error('Couldn\'t clear teams');
    $teams = json_decode(file_get_contents("php://input"));
    foreach ($teams as $members) {
        if (sizeof($members) == 0) continue;
        execute_sql('INSERT INTO mzpc_team (project_id) VALUES (?)', [$_GET['project']]);
        $team_id = latest_id();
        foreach ($members as $member_id) {
            execute_sql('INSERT INTO mzpc_member (team_id, user_id) VALUES (?, ?)', [$team_id, $member_id]);
        }
    }
    die();
}

// catch-all for other request types
error('Unknown request');
