<?php
include('./_include.php'); // this give us some nice features

// GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['section'])) error('No section specified.');
    $result = execute_sql('SELECT id, name, status_open as isOpen FROM mzpc_project WHERE section_id = ?', [$_GET['section']])->fetchAll();
    json($result);
}

// catch-all for other request types
error('Unknown request');
