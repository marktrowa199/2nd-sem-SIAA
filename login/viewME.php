<?php
include 'connect.php';

// Check if the admin session is set
if (!isset($_SESSION['user_name'])) {
    header("location:index.php");
    exit();
}

// Retrieve the admin email from the session
$email = $_SESSION['user_email']; // Now this should be defined

// Query to fetch admin details from the database
$selectAdmin = "SELECT * FROM user_form WHERE email = '$email' AND user_type = 'Member'";
$resultAdmin = mysqli_query($conn, $selectAdmin);

if ($resultAdmin && mysqli_num_rows($resultAdmin) > 0) {
    $adminDetails = mysqli_fetch_assoc($resultAdmin); // Fetch admin details as an associative array
} else {
    // Handle the case where no admin details are found
    die("Admin details not found.");
}

// Query to fetch upcoming events ordered by date and time
$upcomingEventsQuery = "SELECT * FROM events WHERE event_date >= NOW() ORDER BY event_date ASC LIMIT 4";
$resultEvents = mysqli_query($conn, $upcomingEventsQuery);

if ($resultEvents && mysqli_num_rows($resultEvents) > 0) {
    $events = mysqli_fetch_all($resultEvents, MYSQLI_ASSOC); // Fetch events as an associative array
} else {
    $events = []; // No upcoming events found
}

// Query to count the total number of events
$totalEventsQuery = "SELECT COUNT(*) as total FROM events";
$resultTotalEvents = mysqli_query($conn, $totalEventsQuery);

$totalEvents = 0; // Default to zero if the query fails

if ($resultTotalEvents && mysqli_num_rows($resultTotalEvents) > 0) {
    $totalRow = mysqli_fetch_assoc($resultTotalEvents);
    $totalEvents = $totalRow['total']; // Get the total count
}

// Query to fetch upcoming events ordered by date and time
$upcomingEventsQuery = "SELECT * FROM events WHERE event_date >= NOW() ORDER BY event_date ASC LIMIT 4";
$resultEvents = mysqli_query($conn, $upcomingEventsQuery);
$events = $resultEvents ? mysqli_fetch_all($resultEvents, MYSQLI_ASSOC) : [];

// Fetch user event interactions for recommendations
$interactionQuery = "SELECT user_id, event_name FROM user_event_interactions";
try {
    $resultInteractions = mysqli_query($conn, $interactionQuery);
    $interactions = $resultInteractions ? mysqli_fetch_all($resultInteractions, MYSQLI_ASSOC) : [];
} catch (Exception $e) {
    // Handle the case where the table/column doesn't exist
    $interactions = [];
    error_log("Error fetching user_event_interactions: " . $e->getMessage());
}
$currentMonth = date('n'); // Numeric month (1-12)
$query = "SELECT event_name, image_path, description, event_date, 
          DATE_FORMAT(event_time, '%r') AS event_time 
          FROM events 
          WHERE MONTH(event_date) = $currentMonth 
          ORDER BY event_date";

$result = mysqli_query($conn, $query);

$events = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $events[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>View Events</title>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="path/to/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

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
        /* Custom Styles for Confirmation Modal */
        #myModal .modal-content {
        background-color: #f9f9f9;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        border: none;
        }

        #myModal .modal-header {
            background-color: #0e2238;
            color: white;
            border-radius: 10px 10px 0 0;
        }

        #myModal .btn-close {
            filter: invert(1);
        }

        #myModal .modal-body {
            font-size: 1.1rem;
            color: #333;
            padding: 20px;
        }

        #myModal .modal-footer {
            justify-content: center;
            border-top: none;
        }

        #myModal .btn-secondary {
            background-color: #6c757d;
            border: none;
            transition: background-color 0.3s ease;
        }

        #myModal .btn-secondary:hover {
            background-color: #5a6268;
        }

        #myModal .btn-danger {
            background-color: #dc3545;
            border: none;
            transition: background-color 0.3s ease;
        }

        #myModal .btn-danger:hover {
            background-color: #c82333;
        }

        /* css ng modal na nababanas nako */
        #myModal .modal-content {
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border: none;
        }

        /* Modal Header */
        #myModal .modal-header {
            background-color: #007bff; /* Primary color for header */
            color: white;
            border-radius: 10px 10px 0 0;
            padding: 15px;
        }

        #myModal .modal-title {
            font-size: 1.25rem;
            font-weight: bold;
        }

        /* Close Button */
        #myModal .btn-close {
            filter: invert(1); /* Adjusts close button for dark header */
        }

        /* Modal Body */
        #myModal .modal-body {
            font-size: 1rem;
            color: #333;
            padding: 20px;
        }

        /* Modal Footer */
        #myModal .modal-footer {
            display: flex;
            justify-content: space-between;
            padding: 10px 20px;
            border-top: none;
        }

        #myModal .btn-secondary {
            background-color: #6c757d; /* Secondary button color */
            border: none;
            transition: background-color 0.3s ease;
        }

        #myModal .btn-secondary:hover {
            background-color: #5a6268;
        }

        #myModal .btn-primary {
            background-color: #007bff; /* Primary button color */
            border: none;
            transition: background-color 0.3s ease;
        }

        #myModal .btn-primary:hover {
            background-color: #0056b3;
        }

        #recommendations {
            display: none; /* Initially hidden */
            margin-top: 20px;
        }

        #recommendationCards .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
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
                    <a href="#"></br>Welcome! </br> <?php echo htmlspecialchars($adminDetails['fName'] . ' ' . $adminDetails['lName']); ?></a>
                </div>
            </div>
            <br/>
            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="profilemember.php" class="sidebar-link">
                        <i class="lni lni-user"></i>
                        <span>Profile</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="memberdb.php" class="sidebar-link">
                        <i class="lni lni-agenda"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                        data-bs-target="#auth" aria-expanded="false" aria-controls="auth">
                        <i class="lni lni-protection"></i>
                        <span>Check Events</span>
                    </a>
                    <ul id="auth" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                        <li class="sidebar-item">
                            <a href="viewME.php" class="sidebar-link">View Events</a>
                        </li>
                        <li class="sidebar-item">
                            <a href="registered_event.php" class="sidebar-link">Registered Events</a>
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

    <!-- offcanvas -->
    <main class="mt-6 pt-3">
    <!-- Content Area -->
    <div class="content px-3 py-4">
        <div class="container-fluid">
            <!-- Search Form -->
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET" class="d-flex ms-auto mb-4 justify-content-end">
                <input type="text" class="form-control form-control-sm" name="search" placeholder="Search events..." aria-label="Search" style="width: 250px;" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button class="btn btn-outline-primary" type="submit">Search</button>
            </form>

            <!--button para sa show recommendation na button-->
            <?php if (isset($_GET['search']) && !empty(trim($_GET['search']))) : ?>
            <button class="btn btn-success mb-4" id="showRecommendations" style="display: block;">Show Recommendation</button>
            <script>
            document.getElementById('showRecommendations').addEventListener('click', function () {
            // Get the search query (from the form or the input)
            const searchQuery = "<?php echo htmlspecialchars($_GET['search']); ?>";
            const category = "<?php echo isset($_GET['category']) ? htmlspecialchars($_GET['category']) : ''; ?>";
            
            console.log('Search Query:', searchQuery);  // Log search query for debugging
            console.log('Category:', category);

            // Clear the previous recommendations from the table or container
            document.getElementById('recommendationsTable').innerHTML = "";  // Clear previous recommendations

            // Fetch recommendations from PHP based on the search query
            fetch('recommendation.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ searchQuery: searchQuery })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Response Data:', data);  // Log the response data for debugging
                
                if (data.success) {
                    // If recommendations are found, display them
                    const tableHtml = data.recommendations;
                    document.getElementById('recommendationsTable').innerHTML = tableHtml;  // Insert the new table into the div
                } else {
                    alert("No recommendations available. Reason: " + (data.message || "Unknown error"));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("An error occurred while fetching recommendations.");
            });
        });

            
            // Function to register for an event
            function confirmRegistration(eventName) {
                // Placeholder for actual registration logic
                console.log('Attempting to register for:', eventName);

                // Mock registration response
                if (eventName) {
                    alert('You have successfully registered for ' + eventName);
                } else {
                    alert('Error: Event not found'); // This error should now not occur
                }
            }
        </script>

        <!-- Show the table inside a div (will be populated dynamically) -->
        <div id="recommendationsTable"></div>


        <?php endif; ?>
           <!-- Events Section -->
<div class="mb-3">
    <h3 class="fw-bold fs-4 mb-3">Events</h3>
    <hr/>

    <?php
    include 'connect.php';

    function getMonthNumber($date) {
        return date("n", strtotime($date));
    }

    $query = "SELECT event_name, image_path, description, event_date, 
              DATE_FORMAT(event_time, '%H:%i:%s') AS event_time, age_limit, gender_restriction 
              FROM events";

    if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
        $searchTerm = mysqli_real_escape_string($conn, trim($_GET['search']));
        $query .= " WHERE LOWER(event_name) LIKE LOWER('%$searchTerm%')";
    }

    $query .= " ORDER BY event_date";

    $eventsResult = mysqli_query($conn, $query);

    if ($eventsResult && mysqli_num_rows($eventsResult) > 0) {
        $eventsByMonth = [];
        while ($event = mysqli_fetch_assoc($eventsResult)) {
            if (strtotime($event['event_date']) !== false) {
                $monthNumber = getMonthNumber($event['event_date']);
                $monthName = date("F", strtotime($event['event_date']));
                if (!isset($eventsByMonth[$monthNumber])) {
                    $eventsByMonth[$monthNumber] = [
                        'name' => $monthName,
                        'events' => []
                    ];
                }
                $eventsByMonth[$monthNumber]['events'][] = $event;
            }
        }

        ksort($eventsByMonth);

        foreach ($eventsByMonth as $monthNumber => $monthData) {
            echo "<h4 class='fw-bold mt-5'>" . $monthData['name'] . "</h4>";
            echo '<div class="row">';

            foreach ($monthData['events'] as $event) {
                $eventDateTime = strtotime($event['event_date'] . ' ' . $event['event_time']);
                $isExpired = $eventDateTime < time();

                echo '<div class="col-4 mb-4">';
                echo '    <div class="card">';
                echo '        <img src="' . htmlspecialchars($event['image_path'] ?? '') . '" alt="' . htmlspecialchars($event['event_name'] ?? '') . '" class="card-img-top" style="height: 200px; object-fit: cover;">';
                echo '        <div class="card-body">';
                echo '            <h5 class="card-title">' . htmlspecialchars($event['event_name'] ?? '') . '</h5>';
                echo '            <p class="card-text">' . htmlspecialchars($event['event_date'] ?? '') . '</p>';
                echo '            <p class="card-text">' . htmlspecialchars($event['description'] ?? '') . '</p>';
                echo '            <button 
                                class="btn btn-primary" 
                                onclick="showConfirmation(\'' . htmlspecialchars($event['event_name'] ?? '') . '\', \'' . htmlspecialchars($event['event_date'] ?? '') . '\', \'' . htmlspecialchars($event['event_time'] ?? '') . '\')" 
                                ' . ($isExpired ? 'disabled' : '') . '>Register</button>';
                echo '            <button class="btn btn-secondary" onclick="viewEvent(\'' . htmlspecialchars($event['event_name'] ?? '') . '\', \'' . htmlspecialchars($event['image_path'] ?? '') . '\', \'' . htmlspecialchars($event['description'] ?? '') . '\', \'' . htmlspecialchars($event['event_date'] ?? '') . '\', \'' . htmlspecialchars($event['event_time'] ?? '') . '\', \'' . htmlspecialchars($event['age_limit'] ?? '') . '\', \'' . htmlspecialchars($event['gender_restriction'] ?? '') . '\')">View Details</button>';
                echo '        </div>';
                echo '    </div>';
                echo '</div>';
            }
            echo '</div>';
        }
    } else {
        echo "<p>No events found.</p>";
    }

    mysqli_close($conn);
                ?>
            </div>
        </div>
    </div>
</main>

<!-- Modal for View Event -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="eventModalLabel">Event Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Event image -->
        <img id="eventImage" src="" alt="Event Image" class="img-fluid mb-3" style="max-height: 300px; object-fit: cover;">
        
        <!-- Event name -->
        <h5 id="eventTitle"></h5>
        
        <!-- Event description -->
        <p id="eventDetails"></p>
        
        <!--EDAD AT URITANGINA PAG DIKAPA GUMANA-->
        <p id="age"></p>  
        <p id="gender"></p>  
        
        <!-- Event date and time -->
        <p id="eventDate"></p>
        <p id="eventTime"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal for Registration Confirmation -->
<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Register Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to register for this event?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="confirmRegistration('eventName')">Confirm</button>
            </div>
        </div>
    </div>
</div>

  <!-- Scripts -->
        <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- ADD HERE -->

</main>

<!-- Local JS files -->
        <script src="./adminjs/bootstrap.bundle.min.js"></script> <!-- Ensure this file exists -->
        <script src="./adminjs/jquery-3.5.1.js"></script> <!-- Ensure this file exists -->
        <script src="./adminjs/jquery.dataTables.min.js"></script> <!-- Ensure this file exists -->
        <script src="./adminjs/dataTables.bootstrap5.min.js"></script> <!-- Ensure this file exists -->


<script>
// Function to display event details in a modal
function viewEvent(eventName, imagePath, description, eventDate, eventTime, age, gender) {
                document.getElementById('eventTitle').innerText = "Event Name: " + eventName;
                document.getElementById('eventImage').src = imagePath;
                document.getElementById('eventDetails').innerText = "Description: " + description;
                document.getElementById('eventDate').innerText = "Date: " + eventDate;
                document.getElementById('eventTime').innerText = "Time: " + eventTime;
                document.getElementById('age').innerText = "Age: " + (age && age.trim() ? age : 'Not specified');
                document.getElementById('gender').innerText = "Gender: " + (gender && gender.trim() ? gender : 'Not specified');
                
                // Show the modal
                $('#eventModal').modal('show');
            }

            // Function to trigger confirmation modal for registration
function showConfirmation(eventName) {
    const myModal = new bootstrap.Modal(document.getElementById('myModal'));

    // Set the title dynamically
    document.getElementById('myModalLabel').textContent = 'Register for ' + eventName;

    // Set eventName as a data attribute for the modal
    document.getElementById('myModal').setAttribute('data-event-name', eventName);

    // Show the modal
    myModal.show();
}


function confirmRegistration() {
    const eventName = document.getElementById('myModal').getAttribute('data-event-name');

    $.ajax({
        url: 'register_event.php', // PHP file to handle the registration
        type: 'POST',
        data: {
            eventName: eventName
        },
        success: function(response) {
            if (response === 'success') {
                alert('You have successfully registered!');
            } else if (response === 'already_registered') {
                alert('You have already registered for this event.');
            } else {
                alert(response); // Display the error message
            }

            // Close the modal after registration attempt
            const myModal = bootstrap.Modal.getInstance(document.getElementById('myModal'));
            myModal.hide();
        },
        error: function() {
            alert('An error occurred. Please try again.');
        }
    });
}


        </script>

        <script>
            document.querySelector('.toggle-btn').addEventListener('click', function() {
                document.querySelector('#sidebar').classList.toggle('expand');
            });
</script>
</body>

</html>
