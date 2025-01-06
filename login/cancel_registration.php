<?php
include 'connect.php';

// Check if the session is set
if (!isset($_SESSION['user_email'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}

// Retrieve the user email from the session
$email = $_SESSION['user_email'];

// Validate the received event ID
if (isset($_POST['event_id'])) {
    $eventId = intval($_POST['event_id']);
    
    // Check if the user is registered for the specified event
    $checkRegistration = "SELECT * FROM event_registrations WHERE event_id = $eventId AND member_email = '$email'";
    $resultCheck = mysqli_query($conn, $checkRegistration);
    
    if ($resultCheck && mysqli_num_rows($resultCheck) > 0) {
        // Delete the registration
        $deleteRegistration = "DELETE FROM event_registrations WHERE event_id = $eventId AND member_email = '$email'";
        if (mysqli_query($conn, $deleteRegistration)) {
            echo json_encode(['success' => true, 'message' => 'Registration canceled successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to cancel registration.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No registration found for this event.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid event ID.']);
}
?>
