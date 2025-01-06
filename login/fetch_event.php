<?php
// Include database connection
include 'connect.php';

// Get event ID from the AJAX request
if (isset($_GET['id'])) {
    $eventId = intval($_GET['id']); // Ensure event ID is an integer

    // Query to get the event details
    $query = "SELECT * FROM events WHERE id = ?";

    // Prepare statement to avoid SQL injection
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('i', $eventId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $event = $result->fetch_assoc();
            echo json_encode($event); // Return event details as JSON
        } else {
            echo json_encode(['error' => 'Event not found']);
        }

        $stmt->close();
    }
}
mysqli_close($conn);
?>
