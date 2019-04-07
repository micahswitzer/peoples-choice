<?php
header("Access-Control-Allow-Origin: http://localhost:8080");
header('Access-Control-Allow-Credentials: true');

// respond to preflights
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    // return only the headers and not the content
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
        header('Access-Control-Allow-Headers: X-Requested-With, Content-Type');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT');
    }
    exit;
}

function unauthorized() {
    // get rejected!
    http_response_code(401);
    header('Content-Type: text/plain');
    die('Unauthorized'."\n");
}

session_start();
function require_authorized() {
    if (!isset($_SESSION['user_id'])) unauthorized();
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $user_is_admin = $_SESSION['user_is_admin'];
    $user_is_student = $_SESSION['user_is_student'];
}

// define some real nice helper functions...
// returns an error message along with a nice HTTP error code
function error($str) {
    http_response_code(400);
    header('Content-Type: text/plain');
    die('Error: '.$str."\n");
}
// returns the obejct as JSON with the proper content-type
function json($obj) {
    header("Content-Type: application/json");
    echo json_encode($obj)."\n";
    die(); // this is where it all ends
}
// executed the SQL statement provided with the provided params
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
