<?php
include 'connect.php';  // Include your database connection file

if (isset($_GET['event_id'])) {
    $eventId = intval($_GET['event_id']);  // Sanitize the event ID

    $conn->begin_transaction();  // Start a transaction
    try {
        // Delete related registrations first
        $stmt1 = $conn->prepare("DELETE FROM event_registrations WHERE event_id = ?");
        $stmt1->bind_param("i", $eventId);
        $stmt1->execute();

        // Delete the event itself
        $stmt2 = $conn->prepare("DELETE FROM events WHERE id = ?");
        $stmt2->bind_param("i", $eventId);
        $stmt2->execute();

        $conn->commit();  // Commit the transaction
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $conn->rollback();  // Rollback the transaction on error
        echo json_encode(['success' => false, 'message' => 'Error deleting event: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid event ID']);
}
?>
