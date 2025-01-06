<?php
// add_category.php

include 'connect.php'; // Include your database connection

// Set header to return JSON response
header('Content-Type: application/json');

// Read the raw POST data
$rawData = file_get_contents('php://input');

// Debug: Log the raw data to check what is being received
error_log("Raw Data: " . $rawData);

// Decode the JSON data
$data = json_decode($rawData, true);

// Check if JSON was decoded correctly
if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data.']);
    exit;
}

// Check if the category data is present in the decoded JSON
$newCategory = trim($data['category'] ?? '');

// Debug: Check if category is received
if (empty($newCategory)) {
    echo json_encode(['status' => 'error', 'message' => 'Category name is required.']);
    exit;
}

try {
    // Use a prepared statement to prevent SQL injection
    $query = "SELECT COUNT(*) FROM categories WHERE name = ?";
    $stmt = $conn->prepare($query); // Using object-oriented style for consistency

    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $conn->error);
    }

    $stmt->bind_param('s', $newCategory);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        // Category already exists
        echo json_encode(['status' => 'error', 'message' => 'This category already exists.']);
        exit;
    }

    // Insert the new category into the database
    $insertQuery = "INSERT INTO categories (name) VALUES (?)";
    $insertStmt = $conn->prepare($insertQuery);

    if (!$insertStmt) {
        throw new Exception("Failed to prepare insert statement: " . $conn->error);
    }

    $insertStmt->bind_param('s', $newCategory);

    if ($insertStmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Category added successfully.']);
    } else {
        throw new Exception('Database error: Unable to save category.');
    }

    $insertStmt->close();
} catch (Exception $e) {
    // Handle errors gracefully and log them
    error_log("Error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An error occurred while saving the category.']);
} finally {
    // Close the database connection
    $conn->close();
}
?>
