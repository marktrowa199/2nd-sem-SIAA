<?php
// Include the database connection
include('connect.php');

try {
    // Query to fetch event categories from your 'events' table
    $query = "SELECT category FROM events";  // Replace 'events' with your table name
    $stmt = executeQuery($conn, $query);
    $result = $stmt->get_result();

    // Fetch all categories and store them in an array
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row['category'];
    }

    // Return the event categories as JSON
    echo json_encode(['success' => true, 'categories' => $categories]);
} catch (Exception $e) {
    // Return error if there's an issue
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
