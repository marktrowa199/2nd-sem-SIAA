<?php
include 'connect.php'; // Include your database connection file

// Query to get participant counts per month
$query = "
    SELECT 
        DATE_FORMAT(er.registration_date, '%Y-%m') AS month,
        COUNT(er.id) AS participant_count
    FROM event_registrations er
    GROUP BY month
    ORDER BY month ASC
";

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
