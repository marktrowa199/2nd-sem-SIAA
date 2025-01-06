<?php
// update_event.php

include 'connect.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $eventName = mysqli_real_escape_string($conn, $_POST['event_name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $eventDate = mysqli_real_escape_string($conn, $_POST['event_date']);
    $eventTime = mysqli_real_escape_string($conn, $_POST['event_time']);
    $coordinatorName = mysqli_real_escape_string($conn, $_POST['coordinator_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $ageLimit = !empty($_POST['age_limit']) ? intval($_POST['age_limit']) : null;
    $genderRestriction = mysqli_real_escape_string($conn, $_POST['gender_restriction']);

    // Handle file upload (if applicable)
    $imagePath = null;
    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/events/";
        $fileName = basename($_FILES['event_image']['name']);
        $targetFilePath = $targetDir . $fileName;

        // Create the uploads directory if it doesn't exist
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['event_image']['tmp_name'], $targetFilePath)) {
            $imagePath = $targetFilePath;
        }
    }

    // Build the UPDATE query with conditional fields
    $query = "UPDATE events SET 
        event_name = '$eventName',
        category = '$category',
        event_date = '$eventDate',
        event_time = '$eventTime',
        coordinator_name = '$coordinatorName',
        description = '$description',
        age_limit = " . ($ageLimit !== null ? $ageLimit : "NULL") . ",
        gender_restriction = '$genderRestriction'";
        
    // Add image path only if a new image was uploaded
    if ($imagePath) {
        $query .= ", image_path = '$imagePath'";
    }

    $query .= " WHERE id = '$id'";

    // Execute the query
    if (mysqli_query($conn, $query)) {
        echo "success"; // Return success response
    } else {
        echo "Error updating event: " . mysqli_error($conn);
    }
}

// Close the database connection
mysqli_close($conn);
?>
