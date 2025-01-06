<?php
include 'connect.php';

// Check if the admin session is set
if (!isset($_SESSION['admin_name'])) {
    header("location:index.php");
    exit();
}

// Retrieve the admin email from the session
$email = $_SESSION['admin_email']; // Now this should be defined

// Query to fetch admin details from the database
$selectAdmin = "SELECT * FROM user_form WHERE email = '$email' AND user_type = 'Admin'";
$resultAdmin = mysqli_query($conn, $selectAdmin);

if ($resultAdmin && mysqli_num_rows($resultAdmin) > 0) {
    $adminDetails = mysqli_fetch_assoc($resultAdmin); // Fetch admin details as an associative array
} else {
    // Handle the case where no admin details are found
    die("Admin details not found.");
}

// View


?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>View Event</title>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        
         <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">Your Events</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="#slider">Events</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#crudSection">View Events</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section id="slider" class="pt-5">
    <div class="container">
        <h1 class="text-center"><b>Upcoming Events</b></h1>
        <hr />
        <div class="slider">
            <div class="owl-carousel">

                <?php
                // Query to select events from the database
                $query = "SELECT * FROM events"; 
                $eventsResult = mysqli_query($conn, $query);

                // Check if the query was successful
                if ($eventsResult) {
                    // Loop through the events and create slider cards
                    while ($event = mysqli_fetch_assoc($eventsResult)) {
                        // Format the event date and time
                        $formattedDate = date("F j, Y", strtotime($event['event_date']));
                        $formattedTime = date("h:i A", strtotime($event['event_time']));

                        echo '<div class="slider-card">';
                        echo '    <img src="' . htmlspecialchars($event['image_path']) . '" alt="' . htmlspecialchars($event['event_name']) . '">';
                        echo '    <h5 class="text-center"><b>' . htmlspecialchars($event['event_name']) . '</b></h5>';
                        echo '    <p class="text-center">' . htmlspecialchars($event['description']) . '</p>';
                        echo '    <p class="text-center"><b>Date:</b> ' . $formattedDate . '</p>';
                        echo '    <p class="text-center"><b>Time:</b> ' . $formattedTime . '</p>';
                        echo '</div>';
                    }
                    
                } 
                else {
                    echo '<p>Error fetching events: ' . mysqli_error($conn) . '</p>';
                    
                }

                // Close the database connection
                mysqli_close($conn);
                ?>

            </div>
        </div>
    </div>
</section>


    <hr />



    

    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function(){
            // Initialize the carousel
            $(".owl-carousel").owlCarousel({
                loop: true,
                margin: 10,
                nav: true,
                autoplay: true,
                autoplayTimeout: 3500,
                autoplayHoverPause: true,
                center: true,
                navText: [
                    "<i class='fa fa-angle-left'></i>",
                    "<i class='fa fa-angle-right'></i>"
                ],
                responsive: {
                    0: { items: 1 },
                    600: { items: 1 },
                    1000: { items: 3 }
                }
            })
           
        });

    </script>
    <!-- VIEWING PART --> 
    <br>


<section id="crudSection" class="pt-5">
    <h1 class="text-center"><b>All Events</b></h1>


    <!-- Modal for View details  -->
    <?php
include 'connect.php';

// Check if the admin session is set
if (!isset($_SESSION['admin_name'])) {
    header("location:index.php");
    exit();
}

// Query to select events from the database
$query = "
    SELECT events.*, 
           COUNT(event_registrations.event_id) AS participant_count 
    FROM events 
    LEFT JOIN event_registrations ON events.id = event_registrations.event_id 
    GROUP BY events.id
"; 
$eventsResult = mysqli_query($conn, $query);

// Check if the query was successful
if ($eventsResult) {
    if (mysqli_num_rows($eventsResult) > 0) {
        echo '<div class="container mt-4">';
        
        // Initialize a counter for the columns
        $counter = 0;
        echo '<div class="row">'; // Start a new row

        while ($event = mysqli_fetch_assoc($eventsResult)) {
            // Get participant count from the query result
            $participantCount = $event['participant_count'];
            
            echo '<div class="col-4">'; // Start a new column
            echo '<br>';
            echo '    <div class="card">';
            echo '        <img src="' . htmlspecialchars($event['image_path']) . '" alt="' . htmlspecialchars($event['event_name']) . '" class="card-img-top" style="height: 150px; object-fit: cover;">';
            echo '        <div class="card-body">';
            echo '            <h5 class="card-title">' . htmlspecialchars($event['event_name']) . '</h5>';
            echo '            <p class="card-text">' . htmlspecialchars($event['description']) . '</p>';
            echo '            <button class="btn btn-primary" onclick="showEventDetails(\'' . htmlspecialchars($event['event_name']) . '\', \'' . htmlspecialchars($event['event_date']) . '\', \'' . htmlspecialchars($event['event_time']) . '\', \'' . htmlspecialchars($event['description']) . '\', ' . $participantCount . ', ' . $event['id'] . ')">Select</button>';

            echo '        </div>';
            echo '    </div>';
            echo '</div>'; // Close column
            
            // Increment the counter
            $counter++;

            // Close the row after every three columns
            if ($counter % 3 === 0) {
                echo '</div><div class="row">'; // Close current row and start a new one
            }
        }

        // Close the last row if it was not already closed
        echo '</div>'; // Close the last row
        echo '</div>'; // Close the container

    } else {
        echo '<br>';
        echo '<center><h4>No events found.</h4></center>';
    }
} else {
    echo '<p>Error fetching events: ' . mysqli_error($conn) . '</p>';
}
   
// Close the database connection
mysqli_close($conn);
?>

<!-- Modal -->
<div class="modal fade" id="eventDetailsModal" tabindex="-1" aria-labelledby="eventDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventDetailsModalLabel">Event Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Event Name:</strong> <span id="eventName"></span></p>
                <p><strong>Event Date:</strong> <span id="eventDate"></span></p>
                <p><strong>Event Time:</strong> <span id="eventTime"></span></p>
                <p><strong>Description:</strong> <span id="eventDescription"></span></p>
                <p><strong>Participants:</strong> <span id="eventParticipants"></span></p>
            </div>
            <div class="modal-footer">
            <button id="viewParticipantsBtn" type="button" class="btn btn-primary">View Participants</button>

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentEventId = null; // Global variable to store the current event ID

function showEventDetails(name, date, time, description, participantCount, eventId) {
    // Set the modal content
    document.getElementById('eventName').innerText = name;
    document.getElementById('eventDate').innerText = date;
    document.getElementById('eventTime').innerText = time;
    document.getElementById('eventDescription').innerText = description;
    document.getElementById('eventParticipants').innerText = participantCount; // Set the participant count

    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('eventDetailsModal'));
    modal.show();

    // When "View Participants" is clicked, redirect to the participants page with the event_id
    document.getElementById('viewParticipantsBtn').onclick = function () {
    window.location.href = 'participants.php?event_id=' + eventId;
    };
}


</script>

    
 

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-q6fXNj4EFYudnsup/34WffyQ/9pZ4yKB4RbdlffJgIj0hYT/UtLqgKa9aLvxgGIq" crossorigin="anonymous">
    </script>
    <script>
        document.querySelector('.toggle-btn').addEventListener('click', function() {
            document.querySelector('#sidebar').classList.toggle('expand');
        });
    </script>
</body>

</html> 