<?php
session_start();
include 'connect.php';

$userEmail = $_SESSION['user_email']; // Assuming the user is logged in and you have their email stored in session
$eventName = $_POST['eventName'];

// First, retrieve the event ID and the age_limit and gender_restriction from the events table based on the event name
$eventQuery = "SELECT id, age_limit, gender_restriction FROM events WHERE event_name = '$eventName'";
$eventResult = mysqli_query($conn, $eventQuery);

if ($eventResult && mysqli_num_rows($eventResult) > 0) {
    $event = mysqli_fetch_assoc($eventResult);
    $eventId = $event['id'];
    $ageLimit = $event['age_limit'];
    $genderRestriction = $event['gender_restriction'];

        // Retrieve the participant's details (age and gender) from the user_form table
        $userQuery = "SELECT age, gender FROM user_form WHERE email = '$userEmail'";
        $userResult = mysqli_query($conn, $userQuery);

        if ($userResult && mysqli_num_rows($userResult) > 0) {
            $user = mysqli_fetch_assoc($userResult);
            $userAge = $user['age'];
            $userGender = $user['gender'];

            // Check if the participant meets the age_limit
            if ($ageLimit !== NULL && $userAge < $ageLimit) {
                echo 'Sorry, you did not meet the age requirements'; // Participant does not meet age requirement
            }
            // Check if the participant meets the gender_restriction
            elseif ($genderRestriction != 'Both' && $userGender != $genderRestriction) {
                echo 'Sorry, this is designated for different audience'; // Participant does not meet gender requirement
            } else {
                // Check if the user is already registered for this event
                $checkQuery = "SELECT * FROM event_registrations WHERE member_email = '$userEmail' AND event_id = $eventId";
                $checkResult = mysqli_query($conn, $checkQuery);

                if (mysqli_num_rows($checkResult) > 0) {
                    echo 'You are already registered to the event'; // User is already registered
                } else {
                    // Insert the new registration
                    $insertQuery = "INSERT INTO event_registrations (event_id, member_email) VALUES ($eventId, '$userEmail')";
                    if (mysqli_query($conn, $insertQuery)) {
                        echo 'You have successfully registered to the event!'; // Registration successful
                    } else {
                        echo 'error'; // Something went wrong
                    }
                }
            }
        } else {
            echo 'error: User not found'; // User not found
        }
    } else {
        echo 'error: Event not found'; // Event not found
}

mysqli_close($conn);
?>
