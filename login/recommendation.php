<?php
header('Content-Type: application/json');
include 'connect.php'; // Database connection

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    // Capture the incoming JSON payload (search query)
    $request_body = file_get_contents('php://input');
    $data = json_decode($request_body, true);

    if (!isset($data['searchQuery']) || empty(trim($data['searchQuery']))) {
        throw new Exception("Search query is missing or empty.");
    }

    $searchQuery = trim($data['searchQuery']);

    // Prepare SQL query to fetch events based on search query (event name or event date)
    $query = "SELECT * FROM events WHERE event_name LIKE ? OR event_date LIKE ? ORDER BY event_date ASC"; 
    $stmt = $conn->prepare($query);
    $searchParam = "%$searchQuery%";
    $stmt->bind_param("ss", $searchParam, $searchParam);
    $stmt->execute();
    $result = $stmt->get_result();

    $events = [];
    while ($event = $result->fetch_assoc()) {
        $events[] = $event;
    }

    if (empty($events)) {
        throw new Exception("No events found for the search query.");
    }

    // Find the next upcoming event based on the current date
    $currentDate = date('Y-m-d'); // Get today's date
    $recommendedEvent = null;

    // Look for the next event after the current date
    foreach ($events as $event) {
        if (strtotime($event['event_date']) > strtotime($currentDate)) {
            $recommendedEvent = $event;
            break; // Stop once we find the first upcoming event
        }
    }

    // If no upcoming event is found, use the latest event (fallback)
    if ($recommendedEvent === null) {
        $recommendedEvent = $events[0]; // First event in the sorted list (oldest event)
    }

    // Generate the HTML table of events with Bootstrap styling
    $tableHtml = "<table class='table table-bordered table-hover'>
                    <thead>
                        <tr>
                            <th>Event Name</th>
                            <th>Event Date</th>
                            <th>Event Time</th>
                            <th>Description</th>
                            <th>Image</th>
                            
                        </tr>
                    </thead>
                    <tbody>";

    // Loop through the events to display in table rows
    foreach ($events as $event) {
        $imageUrl = !empty($event['image_path']) ? $event['image_path'] : 'default-image.jpg'; // Default image if none exists
        $tableHtml .= "<tr>
            <td>" . htmlspecialchars($event['event_name']) . "</td>
            <td>" . htmlspecialchars($event['event_date']) . "</td>
            <td>" . htmlspecialchars($event['event_time']) . "</td>
            <td>" . htmlspecialchars($event['description']) . "</td>
            <td><img src='" . htmlspecialchars($imageUrl) . "' alt='" . htmlspecialchars($event['event_name']) . "' style='width: 100px; height: 100px; object-fit: cover;'></td>
            <td>
               
                
            </td>
        </tr>";
    }

    $tableHtml .= "</tbody></table>";

    // Send the recommendation response
    echo json_encode([
        'success' => true,
        'recommendations' => $tableHtml
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>
