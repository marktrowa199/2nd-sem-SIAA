<?php
// fetch_events.php

include 'connect.php'; // Include the database connection

// Query to select events from the database
$query = "SELECT * FROM events";
$eventsResult = mysqli_query($conn, $query);

if ($eventsResult) {
    if (mysqli_num_rows($eventsResult) > 0) {
        while ($event = mysqli_fetch_assoc($eventsResult)) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($event['id']) . '</td>';
            echo '<td>' . htmlspecialchars($event['event_name']) . '</td>';
            echo '<td>' . htmlspecialchars($event['category']) . '</td>';
            echo '<td>' . htmlspecialchars($event['event_date']) . '</td>';
            
            // Format event time to 12-hour format
            $eventTime = date("h:i A", strtotime($event['event_time']));
            echo '<td>' . htmlspecialchars($eventTime) . '</td>';
            
            echo '<td>' . htmlspecialchars($event['coordinator_name']) . '</td>';
            echo '<td>' . htmlspecialchars($event['description']) . '</td>';
            
            // Display event image if available
            if (!empty($event['image_path'])) {
                echo '<td><img src="' . htmlspecialchars($event['image_path']) . '" alt="Event Image" style="width: 100px;"></td>';
            } else {
                echo '<td>No image</td>';
            }

            // Display age limit and gender restriction if applicable
            echo '<td>' . htmlspecialchars($event['age_limit'] ?? 'None') . '</td>';
            echo '<td>' . htmlspecialchars($event['gender_restriction'] ?? 'None') . '</td>';

            // Action buttons
            echo '<td>
                    <button class="btn btn-warning editEvent" data-id="' . htmlspecialchars($event['id']) . '" aria-label="Edit Event">
                        <img src="../image/edit.png" alt="Edit" style="width: 16px; height: 16px;">
                    </button>
                    <button class="btn btn-danger deleteEvent" data-id="' . htmlspecialchars($event['id']) . '" aria-label="Delete Event">
                        <img src="../image/delete.png" alt="Delete" style="width: 16px; height: 16px;">
                    </button>
                  </td>';
            echo '</tr>';
        }
    } else {
        // No events found in the database
        echo '<tr><td colspan="11" class="text-center">No events found. <a href="#" class="add-event-link">Click here to add an event.</a></td></tr>';
    }
} else {
    // Error with the query execution
    echo '<tr><td colspan="11" class="text-center text-danger">Error fetching events: ' . mysqli_error($conn) . '</td></tr>';
}

// Close the database connection
mysqli_close($conn);
?>
