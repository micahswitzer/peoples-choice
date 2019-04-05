<?php
include('./_include.php');

// GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    json(execute_sql(
        'SELECT s.points, s.team_id FROM mzpc_score s
        JOIN mzpc_team t ON s.team_id = t.id
        WHERE t.project_id = ? AND s.user_id = ?',
        [$_GET['project'], $user_id])->fetchAll(PDO::FETCH_KEY_PAIR));
}

// POST
else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $medals = json_decode(file_get_contents("php://input"));
    execute_sql('DELETE FROM mzpc_score WHERE user_id = ?', [$user_id]);
    $voter_team_id = execute_sql('SELECT team_id FROM mzpc_member WHERE user_id = ?', [$user_id])->fetch()['team_id'];
    foreach ($medals as $medalValue => $teamId) {
        if (is_null($teamId) or $voter_team_id == $teamId) continue;
        execute_sql('INSERT INTO mzpc_score (user_id, team_id, points) VALUES (?, ?, ?)', [$user_id, $teamId, $medalValue]);
    }
    die();
}

// catch-all for other request types
error('Unknown request');
