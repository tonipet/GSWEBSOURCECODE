<?php
// Database configuration
define('DB_SERVER', 'localhost'); // Database server address
define('DB_USERNAME', 'root'); // Database username
define('DB_PASSWORD', ''); // Database password
define('DB_DATABASE', 'gamesensedb'); // Database name

// Create a connection
function getDB() {
    $dbConnection = null;
    try {
        $dbConnection = new PDO(
            "mysql:host=" . DB_SERVER . ";dbname=" . DB_DATABASE,
            DB_USERNAME,
            DB_PASSWORD
        );
        $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
    return $dbConnection;
}
?>
