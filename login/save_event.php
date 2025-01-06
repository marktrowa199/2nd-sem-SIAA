<?php
// save_event.php

// Set the header to indicate a JSON response
header('Content-Type: application/json');

// Get the input data (assuming it's sent as JSON)
$data = json_decode(file_get_contents('php://input'), true);

// Check if event data is provided
if (isset($data['event_name'], $data['event_date'], $data['event_time'], $data['category']) &&
    !empty($data['event_name']) && !empty($data['event_date']) && !empty($data['event_time']) && !empty($data['category'])) {

    // Get the event data
    $event_name = $data['event_name'];
    $event_date = $data['event_date'];
    $event_time = $data['event_time'];
    $category = $data['category'];

    // Connect to your database
    include 'connect.php';  // Ensure your database connection is set correctly

    // Insert the event into the database (adjust the table name and fields)
    $stmt = $conn->prepare("INSERT INTO events (event_name, event_date, event_time, category) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $event_name, $event_date, $event_time, $category);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Event saved successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save event.']);
    }
    $stmt->close();

    // Close the database connection
    $conn->close();
} else {
    // If required event data is missing
    echo json_encode(['success' => false, 'message' => 'Please provide all required event data.']);
}
?>
