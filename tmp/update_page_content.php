<?php
// Script to update page_content of page ID 12 with the simulator content

// Assuming the database credentials are the same as the application's
require_once dirname(__DIR__) . '/app/Config/Database.php';

// Read the HTML content file
$htmlContent = file_get_contents(dirname(__DIR__) . '/tmp/simulator_content.html');

if (empty($htmlContent)) {
    die("Error: HTML content file is empty or could not be read\n");
}

// Establish database connection
try {
    // Use the Database config from the application
    $config = new \Config\Database();
    $default = $config->default;
    
    $dsn = "mysql:host={$default['hostname']};dbname={$default['database']}";
    $pdo = new PDO($dsn, $default['username'], $default['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Update the page content of page ID 12
    $stmt = $pdo->prepare("UPDATE pages SET page_content = ? WHERE id = 12");
    $result = $stmt->execute([$htmlContent]);
    
    if ($result) {
        echo "Success: Page content updated successfully for page ID 12\n";
    } else {
        echo "Error: Failed to update page content\n";
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage() . "\n");
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
?>