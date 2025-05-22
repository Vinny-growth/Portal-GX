<?php
// Get configuration
$config_file = dirname(__DIR__) . '/app/Config/Database.php';
if (!file_exists($config_file)) {
    die("Config file not found: $config_file\n");
}

// Define some constants needed by the config file
define('SYSTEMPATH', dirname(__DIR__) . '/system/');
define('APPPATH', dirname(__DIR__) . '/app/');
define('ROOTPATH', dirname(__DIR__) . '/');

// Parse the config file for database credentials
$config_content = file_get_contents($config_file);
preg_match("/['|\"]hostname['|\"](\s*)=>(\s*)['|\"](.*?)['|\"]/", $config_content, $hostname_matches);
preg_match("/['|\"]username['|\"](\s*)=>(\s*)['|\"](.*?)['|\"]/", $config_content, $username_matches);
preg_match("/['|\"]password['|\"](\s*)=>(\s*)['|\"](.*?)['|\"]/", $config_content, $password_matches);
preg_match("/['|\"]database['|\"](\s*)=>(\s*)['|\"](.*?)['|\"]/", $config_content, $database_matches);

$hostname = isset($hostname_matches[3]) ? $hostname_matches[3] : 'localhost';
$username = isset($username_matches[3]) ? $username_matches[3] : 'root';
$password = isset($password_matches[3]) ? $password_matches[3] : '';
$database = isset($database_matches[3]) ? $database_matches[3] : '';

if (empty($database)) {
    die("Could not extract database name from config\n");
}

echo "Connecting to database: $database on $hostname as $username\n";

// Connect to the database
try {
    $pdo = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Read the simulator content file
    $simulator_content_file = dirname(__DIR__) . '/tmp/simulator_content_final.html';
    if (!file_exists($simulator_content_file)) {
        die("Simulator content file not found: $simulator_content_file\n");
    }
    
    $html_content = file_get_contents($simulator_content_file);
    if (empty($html_content)) {
        die("Simulator content file is empty\n");
    }
    
    // Update the page_content for page ID 12
    $stmt = $pdo->prepare("UPDATE pages SET page_content = :content WHERE id = 12");
    $result = $stmt->execute(['content' => $html_content]);
    
    if ($result) {
        echo "Success: Page content updated for page ID 12\n";
    } else {
        echo "Error: Failed to update page content\n";
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage() . "\n");
}
?>