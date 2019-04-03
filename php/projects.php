<?php

$sql = "SELECT id, name, status_open as isOpen FROM mzpc_project WHERE section_id = ?";
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