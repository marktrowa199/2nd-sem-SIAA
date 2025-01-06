<?php 

// Ensure the session is not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost'; 
$user = 'root';  
$pass = '';      
$db = 'login_db';  

try {
    $conn = new mysqli($host, $user, $pass, $db);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    // Log the error for debugging purposes
    error_log($e->getMessage(), 3, '/var/log/php_errors.log');
    die("Connection failed: " . $e->getMessage());
}

// Check if the function exists to prevent redeclaration
if (!function_exists('executeQuery')) {
    function executeQuery($conn, $query, $params = [], $types = '') {
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        return $stmt;
    }
}
?>
