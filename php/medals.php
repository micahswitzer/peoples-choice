<?php

$sql = "SELECT p.id as project_id, t.id as team_id, s.score FROM mzpc_project p
        JOIN mzpc_team t ON p.id = t.project_id
        JOIN (SELECT team_id, SUM(points) as score FROM mzpc_score GROUP BY team_id) s ON t.id = s.team_id
        WHERE t.id IN (SELECT st.id FROM mzpc_score ss
               JOIN mzpc_team st ON ss.team_id = st.id
               WHERE st.project_id = p.id
               GROUP BY st.id
               ORDER BY SUM(ss.points) DESC
               LIMIT 3)";

$section_id = $_GET['section'];

//session_start();

$dsn = "mysql:host=james;dbname=cs3220_Sp19;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
// create the PDO object
$pdo = new PDO($dsn, "cs3220", "", $options);

// prepare the statements
$statement = $pdo->prepare($sql);
$statement->execute([$section_id]);
$result = $statement->fetchAll();

// send them results back
header("Access-Control-Allow-Origin: http://localhost:8080");
header("Content-Type: application/json");
echo json_encode($result);