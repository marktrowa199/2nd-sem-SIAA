<?php
include 'connect.php'; // Include your database connection file

// Query to get participant counts per event
$query = "
    SELECT e.event_name, COUNT(er.id) as participant_count 
    FROM events e
    LEFT JOIN event_registrations er ON e.id = er.event_id 
    GROUP BY e.id";

$result = mysqli_query($conn, $query);

$data = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row; // Collect data into an array
    }
}

mysqli_close($conn);
header('Content-Type: application/json'); // Set header for JSON response
echo json_encode($data); // Return data as JSON
?>
