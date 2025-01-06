<?php
session_start();
include 'connect.php';


// Assume this is where you check user credentials
if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute your SQL query here to validate credentials
    $stmt = $conn->prepare("SELECT * FROM user_form WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $loginSuccessful = true; // Login was successful
        // Do something on successful login, e.g., set session variables
        $_SESSION['user_email'] = $email;
        header("Location: profilemember.php"); // Redirect to profile page
        exit();
    } else {
        // Invalid credentials
        $loginSuccessful = false; // Explicitly set this for clarity
    }

    $stmt->close();
}

// At this point, if $loginSuccessful is false, you can show an error message
/*if (!$loginSuccessful) {
    echo "Invalid credentials.";
}*/
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Member Dashboard</title>
    <style>
    body {
        background-color: #f0f4f8; /* Light, soft background */
        font-family: 'Arial', sans-serif;
        color: #333; /* Dark grey text for easy readability */
    }

    /* Navbar */
    .navbar {
        background: #1e96fc;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .navbar-nav .nav-link {
        color: #000000;
    }

    .navbar-nav .nav-link:hover {
        color: #000000;
    }

    /* Header */
    h1 {
        font-size: 2.5rem;
        font-weight: bold;
        color: #000000; 
    }

    /* Cards */
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease-in-out;
        background-color: #ffffff; /* White card background for contrast */
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .card-title {
        font-size: 1.25rem;
        color: #333; /* Dark grey for easy readability */
    }

    .card-text {
        color: #666; /* Soft grey for supporting text */
    }

    .btn-primary {
        background-color: #072ac8; /* Teal buttons */
        border: none;
        transition: background-color 0.2s ease;
    }

    .btn-primary:hover {
        background-color: #006666; /* Darker teal on hover */
    }

     /* Modal styles */
     .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgba(0, 0, 0, 0.4); /* Black w/ opacity */
    }

    .modal-content {
            background-color: #fefefe;
            margin: 15% auto; /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Could be more or less, depending on screen size */
    }

    /* Emoji styles */
    .emoji-container {
        display: flex;
        justify-content: space-around; /* Centered layout */
        margin-bottom: 15px;
    }

    .emoji {
        font-size: 3rem; /* Increased emoji size */
        cursor: pointer;
        transition: transform 0.2s ease, background-color 0.2s ease; /* Smooth transition */
    }

    .emoji.selected {
        background-color: #e0f7fa; /* Light cyan for emoji selection */
        border-radius: 50%;
        padding: 10px;
    }

    .emoji:hover {
        transform: scale(1.3); /* Slightly larger on hover */
        background-color: #d0ece7; /* Light green background on hover */
    }

    .btn-primary {
        background-color: #072ac8; 
        border: none;
        transition: background-color 0.2s ease;
    }

    .btn-primary:hover {
        background-color: #006666; /* Darker teal on hover */
        transform: translateY(-2px); /* Slight lift on hover */
    }

    .close {
        color: #333;
        font-size: 28px;
        cursor: pointer;
    }

    .close:hover {
        color: #555;
    }
    .modal-card {
            max-width: 600px;
        }
    .selected {
            border: 2px solid blue; /* Highlight selected emoji */
        }
</style>

</head>
<body>
    <!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <ul class="navbar-nav me-auto">
            <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="logout.php">Logout</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="memberdb.php">About</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="profilemember.php">Profile</a> <!-- Ensure this line is present -->
            </li>
        </ul>
        <form class="d-flex">
            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success" type="submit">Search</button>
        </form>
    </div>
</nav>


<h1 class="text-center my-4">LocalVista</h1>
<?php
include 'connect.php';

// Query to select events from the database
$query = "SELECT * FROM events"; 
$eventsResult = mysqli_query($conn, $query);

// Check if the query was successful
if ($eventsResult) {
    if (mysqli_num_rows($eventsResult) > 0) {
        echo '<div class="container mt-4">';
        
       
        $counter = 0;
        echo '<div class="row">'; 

        while ($event = mysqli_fetch_assoc($eventsResult)) {
            
            echo '<div class="col-4">'; 
            echo '<br>';
            echo '        <button class="btn btn-info btn-sm mb-2">' . htmlspecialchars($event['category']) . '</button>';
            echo '    <div class="card">';
            echo '        <img src="' . htmlspecialchars($event['image_path']) . '" alt="' . htmlspecialchars($event['event_name']) . '" class="card-img-top" style="height: 200px; object-fit: cover;">';
            echo '        <div class="card-body">';
            echo '            <h5 class="card-title">' . htmlspecialchars($event['event_name']) . '</h5>';
            echo '            <p class="card-text">' . htmlspecialchars($event['description']) . '</p>';
            echo '            <button class="btn btn-primary" onclick="showConfirmation()">Select</button>';
            echo '            <button class="btn btn-primary" onclick="viewEvent(\''.htmlspecialchars($event['event_name']).'\', \''.htmlspecialchars($event['image_path']).'\', \''.htmlspecialchars($event['description']).'\')">View Details</button>';
            echo '        </div>';
            echo '    </div>';
            echo '</div>'; 
            
            $counter++;

            
            if ($counter % 3 === 0) {
                echo '</div><div class="row">'; 
            }
        }

        
        echo '</div>'; 
        echo '</div>'; 

    } else {
        echo '<p>No events found.</p>';
    }
} else {
    echo '<p>Error fetching events: ' . mysqli_error($conn) . '</p>';
}


mysqli_close($conn);
?>


<!-- Modal for confirmation -->
<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Confirm Registration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Do you want to register for this event?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmRegistration()">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for viewing event details -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel">Event Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img id="eventImage" src="" alt="Event Image" class="img-fluid mb-3" style="max-height: 300px; object-fit: cover;">
                <h5 id="eventTitle"></h5>
                <p id="eventDetails"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    let selectedEmoji = ''; // Variable to store selected emoji

    function viewEvent(title, imageSrc, details) {
        // Set the modal content
        document.getElementById('eventTitle').innerText = title;
        document.getElementById('eventImage').src = imageSrc;
        document.getElementById('eventDetails').innerText = details;

        // Show the event modal
        const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
        eventModal.show();
    }

    function showConfirmation() {
        const myModal = new bootstrap.Modal(document.getElementById('myModal'));
        myModal.show();
    }

    function confirmRegistration() {
        alert('You have successfully registered!');
        const myModal = bootstrap.Modal.getInstance(document.getElementById('myModal'));
        myModal.hide();
    }

    function selectEmoji(emoji) {
        selectedEmoji = emoji;
        alert('You selected: ' + emoji);
    }

    function submitFeedback() {
        const feedback = document.getElementById('feedbackTextarea').value;
        alert('Thank you for your feedback!\nEmoji: ' + selectedEmoji + '\nFeedback: ' + feedback);
        const feedbackModal = bootstrap.Modal.getInstance(document.getElementById('feedbackModal'));
        feedbackModal.hide();
    }
</script>
</body>
</html>
