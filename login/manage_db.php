<?php
session_start();
include 'connect.php';

// Check if the admin session is set
if (!isset($_SESSION['admin_name'])) {
    header("location:index.php");
    exit();
}

// Retrieve the admin email from the session
$email = $_SESSION['admin_email']; 

// Query to fetch admin details from the database
$selectAdmin = "SELECT * FROM user_form WHERE email = '$email' AND user_type = 'Admin'";
$resultAdmin = mysqli_query($conn, $selectAdmin);

if ($resultAdmin && mysqli_num_rows($resultAdmin) > 0) {
    $adminDetails = mysqli_fetch_assoc($resultAdmin);
} else {
    die("Admin details not found.");
}
// Handle Add/Edit/Delete event requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $action = $_POST['action'] ?? '';
    $eventName = mysqli_real_escape_string($conn, $_POST['event_name'] ?? '');
    $category = mysqli_real_escape_string($conn, $_POST['category'] ?? '');
    $eventDate = mysqli_real_escape_string($conn, $_POST['event_date'] ?? '');
    $eventTime = mysqli_real_escape_string($conn, $_POST['event_time'] ?? '');
    $coordinatorName = mysqli_real_escape_string($conn, $_POST['coordinator_name'] ?? '');
    $description = mysqli_real_escape_string($conn, $_POST['description'] ?? '');
    $ageLimit = mysqli_real_escape_string($conn, $_POST['age_limit'] ?? ''); // Get the age limit
    $genderRestriction = mysqli_real_escape_string($conn, $_POST['gender_restriction'] ?? ''); // Get the gender restriction

    // Initialize image path
    $imagePath = '';

    // Image upload handling
    if (isset($_FILES['eventImage']) && $_FILES['eventImage']['error'] === UPLOAD_ERR_OK) {
        $image = $_FILES['eventImage'];
        $uploadDir = 'uploads/'; // Directory to save uploaded images

        // Check if the directory exists, create if not
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Validate image type (JPEG, PNG, GIF)
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($image['type'], $allowedTypes)) {
            die('Error: Invalid file type. Only JPG, PNG, and GIF are allowed.');
        }

        // Generate a unique file name to avoid overwriting
        $imageExtension = pathinfo($image['name'], PATHINFO_EXTENSION);
        $newFileName = uniqid() . '.' . $imageExtension;
        $targetFile = $uploadDir . $newFileName;

        // Move the uploaded file to the server
        if (move_uploaded_file($image['tmp_name'], $targetFile)) {
            $imagePath = $targetFile; // Store the image path if uploaded successfully
        } else {
            die('Error: Failed to upload the image.');
        }
    }

    // Check if required fields are provided
    if (empty($eventName) || empty($category) || empty($eventDate) || empty($eventTime) || empty($coordinatorName)) {
        die('Error: Missing required fields.');
    }

    if ($action === 'add') {
        // Add new event
        $insertEvent = "INSERT INTO events (event_name, category, event_date, event_time, coordinator_name, description, image_path, eventBooker, age_limit, gender_restriction) 
                        VALUES ('$eventName', '$category', '$eventDate', '$eventTime', '$coordinatorName', '$description', '$imagePath', '', '$ageLimit', '$genderRestriction')";
        if (!mysqli_query($conn, $insertEvent)) {
            die("Error inserting event: " . mysqli_error($conn));
        }
    } elseif ($action === 'edit') {
        $eventId = $_POST['event_id'] ?? 0;
        if ($eventId <= 0) {
            die('Error: Invalid event ID.');
        }

        // Edit existing event
        $updateEvent = "UPDATE events 
                        SET event_name = '$eventName', category = '$category', event_date = '$eventDate', event_time = '$eventTime', coordinator_name = '$coordinatorName', description = '$description', age_limit = '$ageLimit', gender_restriction = '$genderRestriction'";

        // Only update the image if it was uploaded
        if ($imagePath) {
            $updateEvent .= ", image_path = '$imagePath'";
        }

        $updateEvent .= " WHERE id = $eventId";
        if (!mysqli_query($conn, $updateEvent)) {
            die("Error updating event: " . mysqli_error($conn));
        }
    } elseif ($action === 'delete') {
        $eventId = $_POST['event_id'] ?? 0;
        if ($eventId <= 0) {
            die('Error: Invalid event ID.');
        }

        // Delete event
        $deleteEvent = "DELETE FROM events WHERE id = $eventId";
        if (!mysqli_query($conn, $deleteEvent)) {
            die("Error deleting event: " . mysqli_error($conn));
        }
    }

    // Exit script after processing the request
    exit;
}

// Fetch events for display
$selectEvents = "SELECT * FROM events ORDER BY created_at DESC";
$eventsResult = mysqli_query($conn, $selectEvents);
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Manage Event</title>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
     /* Add your custom styles here */
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+HK&display=swap');
        html {
            scroll-behavior: smooth;
        }
        body {
            font-family: 'Noto Sans HK', sans-serif;
            background: #fff;
        }
        .slider {
            margin-bottom: 30px;
            position: relative;
        }
        .slider .owl-item.active.center .slider-card {
            transform: scale(1.15);
            opacity: 1;
            background: #AFCBFF;
            color: #fff;
        }
        .slider-card {
            background: #fff;
            padding: 0px 0px;
            margin: 50px 15px 90px 15px;
            border-radius: 5px;
            box-shadow: 0 15px 45px -20px rgb(0 0 0 / 73%);
            transform: scale(0.9);
            opacity: 0.5;
            transition: all 0.3s;
        }
        .slider-card img {
            border-radius: 5px 5px 0px 0px;
        }
        .owl-nav .owl-prev {
            position: absolute;
            top: calc(50% - 25px);
            left: 0;
            opacity: 1;
            font-size: 30px !important;
            z-index: 1;
        }
        .owl-nav .owl-next {
            position: absolute;
            top: calc(50% - 25px);
            right: 0;
            opacity: 1;
            font-size: 30px !important;
            z-index: 1;
        }
        .owl-dots {
            text-align: center;
        }
        .owl-dots .owl-dot {
            height: 10px;
            width: 10px;
            border-radius: 10px;
            background: #ccc !important;
            margin-left: 3px;
            margin-right: 3px;
            outline: none;
        }
        .owl-dots .owl-dot.active {
            background: #f44336 !important;
        }
        ::after,
        ::before {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        a {
            text-decoration: none;
        }

        li {
            list-style: none;
        }

        body {
            font-family: 'Poppins', sans-serif;
        }

        .wrapper {
            display: flex;
        }

        .main {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width: 100%;
            overflow: hidden;
            transition: all 0.35s ease-in-out;
            background-color: #fff;
            min-width: 0;
        }

        #sidebar {
            width: 70px;
            min-width: 70px;
            z-index: 1000;
            transition: all .25s ease-in-out;
            background-color: #0e2238;
            display: flex;
            flex-direction: column;
        }

        #sidebar.expand {
            width: 260px;
            min-width: 260px;
        }

        .toggle-btn {
            background-color: transparent;
            cursor: pointer;
            border: 0;
            padding: 1rem 1.5rem;
        }

        .toggle-btn i {
            font-size: 1.5rem;
            color: #FFF;
        }

        .sidebar-logo {
            margin: auto 0;
        }

        .sidebar-logo a {
            color: #FFF;
            font-size: 1.15rem;
            font-weight: 600;
        }

        #sidebar:not(.expand) .sidebar-logo,
        #sidebar:not(.expand) a.sidebar-link span {
            display: none;
        }

        #sidebar.expand .sidebar-logo,
        #sidebar.expand a.sidebar-link span {
            animation: fadeIn .25s ease;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        .sidebar-nav {
            padding: 2rem 0;
            flex: 1 1 auto;
        }

        a.sidebar-link {
            padding: .625rem 1.625rem;
            color: #FFF;
            display: block;
            font-size: 0.9rem;
            white-space: nowrap;
            border-left: 3px solid transparent;
        }

        .sidebar-link i,
        .dropdown-item i {
            font-size: 1.1rem;
            margin-right: .75rem;
        }

        a.sidebar-link:hover {
            background-color: rgba(255, 255, 255, .075);
            border-left: 3px solid #3b7ddd;
        }

        .sidebar-item {
            position: relative;
        }

        #sidebar:not(.expand) .sidebar-item .sidebar-dropdown {
            position: absolute;
            top: 0;
            left: 70px;
            background-color: #0e2238;
            padding: 0;
            min-width: 15rem;
            display: none;
        }

        #sidebar:not(.expand) .sidebar-item:hover .has-dropdown+.sidebar-dropdown {
            display: block;
            max-height: 15em;
            width: 100%;
            opacity: 1;
        }

        #sidebar.expand .sidebar-link[data-bs-toggle="collapse"]::after {
            border: solid;
            border-width: 0 .075rem .075rem 0;
            content: "";
            display: inline-block;
            padding: 2px;
            position: absolute;
            right: 1.5rem;
            top: 1.4rem;
            transform: rotate(-135deg);
            transition: all .2s ease-out;
        }

        #sidebar.expand .sidebar-link[data-bs-toggle="collapse"].collapsed::after {
            transform: rotate(45deg);
            transition: all .2s ease-out;
        }

        .navbar {
            background-color: #f5f5f5;
            box-shadow: 0 0 2rem 0 rgba(33, 37, 41, .1);
        }

        .navbar-expand .navbar-collapse {
            min-width: 200px;
        }

        .avatar {
            height: 40px;
            width: 40px;
        }
       
         .button-container {
                display: inline-flex;
                gap: 5px; /* Adjust the space between buttons */
            }

            /*CSS for add/edit modal */
        #eventModal .modal-dialog {
            max-width: 35%; /* Adjust the width to make the modal rectangular */
            width: auto;
        }

        #eventModal .modal-body {
            display: flex;
            flex-direction: row; /* Ensures the content is arranged horizontally */
            justify-content: space-between; /* Adjust spacing */
        }

        #eventModal .modal-body .col-6 {
            padding: 100px; /* Optional: Adjust padding if needed */
        }


        @media (min-width: 768px) {}
    </style>
</head>

<body>
    <div class="wrapper">
        <aside id="sidebar">
            <div class="d-flex">
                <button class="toggle-btn" type="button">
                    <i class="lni lni-grid-alt"></i>
                </button>
                <div class="sidebar-logo">
                    <a href="#"></br>Welcome! </br> Admin <?php echo htmlspecialchars($adminDetails['fName'] . ' ' . $adminDetails['lName']); ?></a>
                </div>
            </div>
            <br/>
            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="adminprofile.php" class="sidebar-link">
                        <i class="lni lni-user"></i>
                        <span>Profile</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="admin_db.php" class="sidebar-link">
                        <i class="lni lni-agenda"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                        data-bs-target="#auth" aria-expanded="false" aria-controls="auth">
                        <i class="lni lni-cog"></i>
                        <span>Manage Events</span>
                    </a>
                    <ul id="auth" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                        <li class="sidebar-item">
                            <a href="manage_db.php" class="sidebar-link">Edit Events</a>
                        </li>
                        <li class="sidebar-item">
                            <a href="view_db.php" class="sidebar-link">View Events</a>
                        </li>
                    </ul>
                </li>


 
                <li class="sidebar-item">
                <a href="logout.php" class="sidebar-link">
                    <i class="lni lni-exit"></i>
                    <span>Logout</span>
                </a>
                </li>
            </ul>
       
        </aside>
        <div class="main">

        
            
              <!-- top navigation bar -->
    
              <!-- top navigation bar -->

    <!-- offcanvas -->
   
    <main class="mt-6 pt-3">
        
    <section id="crudSection" class="pt-5">
    <div class="container">
        <a href="admin_db.php" class="text-decoration-none">
            <i class='bx bx-arrow-back' style="font-size: 2rem;"></i> <!-- Adjusted size -->
        </a>
        <h2 class="text-center">Manage Event</h2>
        <hr />
        <div class="d-flex justify-content-end gap-2 mb-3">
            <button class="btn btn-primary" id="addEventButton">Add Event</button>
            <button class="btn btn-secondary" id="addCategoryButton">Add Category</button>
        </div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Event Name</th>
                    <th>Category</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Event Coordinator</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Age Requirement</th>
                    <th>Gender Restriction</th> <!-- New column for image -->
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="eventTableBody">
                <?php
                if ($eventsResult && mysqli_num_rows($eventsResult) > 0) {
                    while ($event = mysqli_fetch_assoc($eventsResult)) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($event['id']) . '</td>';
                        echo '<td>' . htmlspecialchars($event['event_name']) . '</td>';
                        echo '<td>' . htmlspecialchars($event['category']) . '</td>';
                        echo '<td>' . htmlspecialchars($event['event_date']) . '</td>';
                        echo '<td>' . htmlspecialchars($event['event_time']) . '</td>';
                        echo '<td>' . htmlspecialchars($event['coordinator_name']) . '</td>';
                        echo '<td>' . htmlspecialchars($event['description']) . '</td>';
                        echo '<td><img src="' . htmlspecialchars($event['image_path']) . '" alt="Event Image" style="width: 100px;"></td>'; // Display image
                        echo '<td>
                                    <div class="button-container">
                                        <button class="btn btn-warning editEvent" data-id="' . htmlspecialchars($event['id']) . '">Edit</button>
                                        <button class="btn btn-danger deleteEvent" data-id="' . htmlspecialchars($event['id']) . '">Delete</button>
                                    </div>
                                </td>';

                    }
                } else {
                    echo '<tr><td colspan="9" class="text-center">No events found.</td></tr>';
                }
                ?>
            </tbody>
        </table>
         </div>
    </section>


  <!-- Modal for Adding/Editing Event -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel">Add/Edit Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="eventForm" enctype="multipart/form-data" method="POST">
                    <input type="hidden" name="action" value="add"> <!-- Ensure to set action -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="eventName" class="form-label">Event Name</label>
                            <input type="text" class="form-control" id="eventName" name="event_name" required>
                        </div>
                        <div class="col-md-6">
                        <label for="category">Category:</label>
                            <select id="categoryDropdown" name="category" class="form-control">
                                <!-- Options will be populated dynamically -->
                            </select>

                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="event_date" required>
                        </div>
                        <div class="col-md-6">
                            <label for="time" class="form-label">Time</label>
                            <input type="time" class="form-control" id="time" name="event_time" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="fullName" class="form-label">Event Coordinator</label>
                        <input type="text" class="form-control" id="fullName" name="coordinator_name" required>
                    </div>

                    <div class="mb-3">
                        <label for="eventDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="eventDescription" name="description" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="eventImage" class="form-label">Event Image</label>
                        <input type="file" class="form-control" id="eventImage" name="eventImage" accept="image/*">
                    </div>

                    <!-- Age Requirement Section -->
                    <div class="mb-3">
                    <label for="ageLimit">Age Limit:</label>
                    <input type="number" id="ageLimit" name="age_limit" class="form-control">
                    </div>

                    <!-- Gender Restriction Section -->
                    <div class="mb-3">
                    <label for="gender_restriction">Gender Restriction:</label>
                    <select id="genderRestriction" name="gender_restriction" class="form-control">
                        <option value="Both">Both</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                    </div>

                    <input type="hidden" id="eventId" name="event_id"> <!-- Hidden field for event ID in edit mode -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="saveEventButton">Save Event</button>
            </div>
        </div>
    </div>
</div>

<!-- Add category -->
<div id="addCategoryModal" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="newCategoryInput" class="form-control" placeholder="Enter new category name">
            </div>
            <div class="modal-footer">
                <button id="saveCategoryButton" class="btn btn-success">Save</button>
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



    <!-- Existing Script Tags -->

<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {

// WebSocket connection initialization
    var ws = new WebSocket('ws://127.0.0.1:8080');

            ws.onopen = function() {
                console.log('WebSocket connection established.');
            };

            ws.onmessage = function(event) {
                console.log('Message from server:', event.data);
            };

            ws.onerror = function(error) {
                console.error('WebSocket Error:', error);
            };

        // Show modal when Add Event button is clicked
            $('#addEventButton').on('click', function() {
                // Reset form data for adding new event
                $('#eventForm')[0].reset();
                $('#eventId').val(''); // Clear event ID for new event
                $('#eventModalLabel').text('Add Event'); // Update modal title
                $('#eventModal').modal('show'); // Show modal
            });

// Save event (add/edit)
$('#saveEventButton').on('click', function() {
    const formData = new FormData($('#eventForm')[0]); // Create FormData object from the form

    // If an event ID is present, set action to 'edit'
    if ($('#eventId').val()) {
        formData.set('action', 'edit');
    } else {
        formData.set('action', 'add');
    }

    // Log form data to check if it's correct
    for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }

    // AJAX request to save the event
    $.ajax({
        url: 'manage_db.php', // Ensure this matches your PHP file
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            console.log('Server response:', response);
            // Reload the event table or handle the response if needed
            loadEvents();
            $('#eventModal').modal('hide');
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
        }
    });
});

// Event delegation for editing and deleting events
$('#eventTableBody').on('click', '.editEvent', function() {
    const eventId = $(this).data('id'); // Get event ID from data attribute

    // Fetch event details for editing
    $.ajax({
        url: 'fetch_event.php', // Create this PHP file to fetch specific event details
        type: 'GET',
        data: { id: eventId },
        success: function(event) {
            console.log('Event data fetched:', event);

            $('#eventModalLabel').text('Edit Event');
            $('#eventName').val(event.event_name);
            $('#category').val(event.category);
            $('#date').val(event.event_date);
            $('#time').val(event.event_time);
            $('#fullName').val(event.coordinator_name);
            $('#eventDescription').val(event.description);
            $('#eventId').val(event.id);
            $('#ageLimit').val(event.age_limit); // Set age limit
            $('#genderRestriction').val(event.gender_restriction); // Set gender restriction
            $('#eventModal').modal('show');
        },
        error: function(xhr, status, error) {
            console.error('Error fetching event details:', error);
        }
    });
});

// delete event
$('#eventTableBody').on('click', '.deleteEvent', function() {
    const eventId = $(this).data('id'); // Get event ID from data attribute

    // Step 1: Check if the event has participants
    $.ajax({
        url: 'participants.php', // Script to check for participants
        type: 'GET',
        data: { event_id: eventId },
        success: function(response) {
            if (response.hasParticipants) {
                // Step 2: Show confirmation dialog if participants exist
                const confirmDelete = confirm(`This event has ${response.participantCount} participant(s). Do you still want to delete it?`);
                if (confirmDelete) {
                    // Step 3: Proceed to delete the event
                    deleteEvent(eventId);
                }
            } else {
                // Step 3: No participants, delete directly
                deleteEvent(eventId);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error checking participants:', error);
        }
    });

    // Function to delete the event
    function deleteEvent(eventId) {
        $.ajax({
            url: 'delete_event.php', // Change this to use delete_event.php
            type: 'GET',  // Use GET for a direct URL query
            data: { event_id: eventId },
            success: function(response) {
                console.log('Event deleted response:', response);
                loadEvents();  // Refresh events table after deletion
            },
            error: function(xhr, status, error) {
                console.error('Error deleting event:', error);
            }
        });
    }
});




// Set min attribute for date input to prevent selecting past dates
const today = new Date().toISOString().split('T')[0];
document.getElementById('date').setAttribute('min', today);

// Sidebar toggle functionality
document.querySelector('.toggle-btn').addEventListener('click', function() {
    document.querySelector('#sidebar').classList.toggle('expand');
});

// Function to load events from fetch_events.php
function loadEvents() {
    $.ajax({
        url: 'fetch_events.php', // Path to your PHP file
        type: 'GET',
        success: function(response) {
            $('#eventTableBody').html(response); // Populate the table body with the fetched data
        },
        error: function(xhr, status, error) {
            console.error('Error fetching events:', error);
        }
    });
}

// Call the function to load events on page load
loadEvents();

});

$(document).on('click', '.editEvent', function() {
    var eventId = $(this).data('id');

    // Fetch the event details using AJAX
    $.ajax({
        url: 'fetch_event.php', // File responsible for fetching event details
        type: 'GET',
        data: { id: eventId },
        success: function(data) {
            var event = JSON.parse(data);
            $('#eventId').val(event.id);
            $('#eventName').val(event.event_name);
            $('#eventCategory').val(event.category);
            $('#eventDate').val(event.event_date);
            $('#eventTime').val(event.event_time);
            $('#coordinatorName').val(event.coordinator_name);
            $('#eventDescription').val(event.description);
            $('#editEventModal').modal('show');
        }
    });
});


    // Handle form submission for editing the event
    $('#editEventForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'update_event.php', // A PHP file to update the event in the database
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                // Refresh the event list or handle the response as needed
                location.reload(); // Reload the page to see the updated events
            }
        });
    });
    // Event listener for the add event link
$(document).on('click', '.add-event-link', function(e) {
    e.preventDefault(); // Prevent default anchor behavior
    $('#eventForm')[0].reset(); // Reset form data for adding a new event
    $('#eventId').val(''); // Clear event ID for new event
    $('#eventModalLabel').text('Add Event'); // Update modal title
    $('#eventModal').modal('show'); // Show modal
});

// add category
document.addEventListener('DOMContentLoaded', () => {
    const categoryDropdown = document.getElementById('categoryDropdown');
    const addCategoryButton = document.getElementById('addCategoryButton');
    const addCategoryModal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
    const newCategoryInput = document.getElementById('newCategoryInput');
    const saveCategoryButton = document.getElementById('saveCategoryButton');

    // Fetch categories from the server
    function fetchCategories() {
        fetch('fetch_categories.php')
            .then(response => response.json())
            .then(categories => {
                categoryDropdown.innerHTML = '';
                categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.name;
                    option.textContent = category.name;
                    categoryDropdown.appendChild(option);
                });
            })
            .catch(err => console.error('Error fetching categories:', err));
    }

    // Show add category modal
    addCategoryButton.addEventListener('click', () => {
        newCategoryInput.value = '';
        addCategoryModal.show();
    });

    // Save new category with duplicate check
    saveCategoryButton.addEventListener('click', () => {
        const newCategory = newCategoryInput.value.trim();
        if (newCategory) {
            fetch('check_category_exists.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ category: newCategory })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'error' && data.message === 'This category already exists.') {
                        alert(data.message); // Show alert if category already exists
                    } else if (data.status === 'success') {
                        // If no duplicate, proceed to add category
                        fetch('add_category.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ category: newCategory })
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    addCategoryModal.hide();
                                    fetchCategories(); // Reload categories after adding
                                } else {
                                    alert(data.message); // Show error message
                                }
                            })
                            .catch(err => console.error('Error adding category:', err));
                    }
                })
                .catch(err => console.error('Error checking category:', err));
        } else {
            alert('Category name cannot be empty.');
        }
    });

    // Initial load of categories
    fetchCategories();
});


</script>
<!-- Bootstrap bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="CORRECT_HASH_HERE" crossorigin="anonymous"></script>

</body>

</html>