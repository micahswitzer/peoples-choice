<?php
include('./_include.php');

$voter_team_id = execute_sql('SELECT u.id FROM mzpc_user AS u JOIN mzpc_member AS m ON u.id = m.user_id WHERE m.team_id = ?', [$_GET['team_id']]);
$vote_self = $_GET['user_id'];
if (($vote_self == $voter_team_id) or !$user_is_student) unauthorized();

//took out these errors as they apply to both post and put
if (!isset($_POST['user_id'])) error('User id not specified.');
if (!isset($_POST['team_id'])) error('Team id not specified.');
if (!isset($_POST['points'])) error('Medal not specified.');

// POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    execute_sql('INSERT INTO mzpc_score (user_id, team_id, points) VALUES (?, ?, ?)', [$_POST['user_id'], $_POST['team_id'], $_POST['points']]);
}

//reassign medals
// PUT
else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $put); // get the contents of the PUT request
    execute_sql('UPDATE mzpc_score SET points = ? WHERE user_id = ? AND team_id = ?', [$_POST['points'], $_POST['user_id'], $_POST['team_id']]);
}

// catch-all for other request types
error('Unknown request');
