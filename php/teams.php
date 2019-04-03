<?php

function error($str) {
    header('Content-Type: text/plain');
    die('Error: '.$str);
}
function json($obj) {
    header("Access-Control-Allow-Origin: http://localhost:8080");
    header("Content-Type: application/json");
    echo json_encode($obj);
    die();
}

session_start();

function execute_sql($sql_stmnt, $sql_params) {
    $pdo = new PDO("mysql:host=james;dbname=cs3220_Sp19;charset=utf8mb4", "cs3220", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    $statement = $pdo->prepare($sql_stmnt) or error('Couldn\'t create prepared statement.');
    $statement->execute($sql_params) or error('Couldn\'t execute prepared statement.');
    return $statement;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['project'])) {
        error('No project specified.');
    }
    $project_id = $_GET['project'];
    $result = execute_sql('SELECT id, name FROM mzpc_team WHERE project_id = ?', [$project_id])->fetchAll();
    json($result);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['name'])) error('Name not specified.');
    if (!isset($_POST['project_id'])) error('Project id not specified.');
    $results = execute_sql('INSERT INTO mzpc_team (name, project_id) VALUES (?, ?)', [$_POST['name'], $_POST['project_id']])->fetchAll();
    json($result);
}
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $put); // get the contents of the PUT request
    if (!isset($_GET['id'])) error('Team id not specified.');
    if (!isset($put['name'])) error('Name not specified.');
    $results = execute_sql('UPDATE mzpc_team SET name = ? WHERE id = ?', [$put['name'], $_GET['id']])->fetchAll();
    json($result);
}