<?php
include 'connect.php';

// Check if the event_id is passed in the URL
if (isset($_GET['event_id']) && !empty($_GET['event_id'])) {
    $event_id = $_GET['event_id'];

    // Sanitize the event_id to prevent SQL injection
    $event_id = mysqli_real_escape_string($conn, $event_id);

    // Query to fetch participants for the specific event
    $query = "
        SELECT user_form.fName, user_form.lName, user_form.email 
        FROM user_form
        INNER JOIN event_registrations ON user_form.email = event_registrations.member_email
        WHERE event_registrations.event_id = '$event_id'
    ";

    // Execute the query
    $result = mysqli_query($conn, $query);

    // Start main container
    echo "<div style='max-width: 80%; margin: 40px auto; padding: 30px; border: 1px solid #ddd; border-radius: 8px; background-color: #fafafa; position: relative;'>";

    // Close (cross) icon with larger size and redirect link
    echo "<a href='view_db.php' style='position: absolute; top: 15px; right: 15px; font-size: 36px; text-decoration: none; color: #555;' title='Close'>";
    echo "&times;";
    echo "</a>";

    // Main header for the participants table
    echo "<h1 style='text-align: center; color: #333; margin-bottom: 20px;'>Event's Participants</h1>";

    // Display participant details in a table if found
    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            echo "<table style='width: 100%; border-collapse: collapse; margin-top: 20px;'>";
            echo "<thead>";
            echo "<tr style='background-color: #f2f2f2; text-align: left;'>";
            echo "<th style='padding: 12px; border-bottom: 1px solid #ddd;'>First Name</th>";
            echo "<th style='padding: 12px; border-bottom: 1px solid #ddd;'>Last Name</th>";
            echo "<th style='padding: 12px; border-bottom: 1px solid #ddd;'>Email</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

            // Populate rows with participant data
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr style='border-bottom: 1px solid #ddd;'>";
                echo "<td style='padding: 12px;'>" . htmlspecialchars($row['fName']) . "</td>";
                echo "<td style='padding: 12px;'>" . htmlspecialchars($row['lName']) . "</td>";
                echo "<td style='padding: 12px;'>" . htmlspecialchars($row['email']) . "</td>";
                echo "</tr>";
            }

            echo "</tbody>";
            echo "</table>";
        } else {
            // Message for no participants
            echo "<p style='text-align: center; color: #666;'>No participants found for this event.</p>";
        }
    } else {
        echo "<p style='text-align: center; color: red;'>Error fetching participants: " . mysqli_error($conn) . "</p>";
    }

    // Close main container
    echo "</div>";
} else {
    echo "<p style='text-align: center; color: red;'>No event ID specified.</p>";
}

mysqli_close($conn);
?>
