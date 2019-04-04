<?php
include('./_include.php');

// catch-all for other request types since we only support GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') error('Unknown request type.');

json(execute_sql('SELECT id FROM mzpc_section')->fetchAll(PDO::FETCH_COLUMN, 0));
