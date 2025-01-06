<?php
include 'connect.php';  // Include your database connection file

if (isset($_GET['event_id'])) {
    $eventId = intval($_GET['event_id']);  // Sanitize the event ID

    // Query to count participants for the specified event
    $query = "SELECT COUNT(*) AS participantCount FROM event_registrations WHERE event_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    // Return JSON response
    echo json_encode([
        'hasParticipants' => $data['participantCount'] > 0,
        'participantCount' => $data['participantCount']
    ]);
} else {
    // Invalid request
    echo json_encode(['error' => 'Invalid event ID']);
}
?>
