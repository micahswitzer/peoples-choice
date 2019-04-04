<?php
include('./_include.php');

// catch-all for other request types since we only support GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') error('Unknown request type.');

if (!isset($_GET['project'])) error('Project id not specified.');

json(execute_sql(
    'SELECT team_id, points, COUNT(*) as count
    FROM mzpc_score 
    WHERE team_id IN
        (SELECT id FROM mzpc_team
        WHERE project_id = ?)
    GROUP BY team_id, points', [$_GET['project']])->fetchAll(PDO::FETCH_GROUP));
