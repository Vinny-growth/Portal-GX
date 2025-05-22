<?php
// Set necessary server variables for CLI execution
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/admin/updateSimulatorPage';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_METHOD'] = 'GET';

// Run the application
require_once dirname(__DIR__) . '/index.php';
?>