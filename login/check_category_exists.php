<?php
// check_category_exists.php ETO AY PARA HINDI MA DOBLE YUNG EVENT OR MA DUPLICATE!!!!

include 'connect.php'; // Include your database connection

// Set header to return JSON response
header('Content-Type: application/json');

// Read the raw POST data
$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

// Validate input data
$categoryName = trim($data['category'] ?? '');

if (empty($categoryName)) {
    echo json_encode(['status' => 'error', 'message' => 'Category name is required.']);
    exit;
}

try {
    // Check if the category already exists
    $query = "SELECT COUNT(*) FROM categories WHERE name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $categoryName);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        // If category exists, return error
        echo json_encode(['status' => 'error', 'message' => 'This category already exists.']);
    } else {
        // No duplicate found, return success
        echo json_encode(['status' => 'success']);
    }
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An error occurred while checking the category.']);
} finally {
    $conn->close();
}
?>
