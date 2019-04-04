<?php

function unauthorized() {
    // get rejected!
    http_response_code(401);
    header('Content-Type: text/plain');
    die('Unauthorized'."\n");
}
// check if the user is logged in
session_start();
// DEV ONLY, FIX FOR PRODUCTION
if (false and !isset($_SESSION['user_id'])) unauthorized();
else {
    $user_id = '15';//$_SESSION['user_id'];
    $user_is_admin = true;//$_SESSION['user_is_admin']; // this will come from the DB when a user logs in
    $user_is_student = true;//$_SESSION['user_is_student']; // more testing
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
    header("Access-Control-Allow-Origin: http://localhost:8080");
    header("Content-Type: application/json");
    echo json_encode($obj);
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
