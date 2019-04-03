<?php
include('./_include.php');

// catch-all for other request types since we only support GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') error('Unknown request type.');
if (!isset($_GET['section'])) error('No section specified.');

$projects = execute_sql('SELECT id FROM mzpc_project WHERE section_id = ?', [$_GET['section']])->fetchAll(PDO::FETCH_COLUMN, 0);

$results = [];
foreach ($projects as $project_id) {
    $results[$project_id] = execute_sql(
        'SELECT s.team_id, SUM(s.points) as score
        FROM mzpc_score s
        JOIN mzpc_team t ON s.team_id = t.id
        WHERE t.project_id = ?
        GROUP BY s.team_id
        ORDER BY SUM(s.points) DESC
        LIMIT 3', [$project_id])->fetchAll();
}
json($results);
