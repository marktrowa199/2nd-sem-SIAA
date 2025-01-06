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
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Registered Event</title>
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
        /* wot */
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
    position: relative;
    width: 85%; /* Adjust this value to make it bigger or smaller */
    height: 525px; /* Increase height to make the image larger */
    margin: 0 auto; /* Center the image */
    border-radius: 10px;
    transition: transform 0.5s ease-in-out;
}

.image-container {
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
}

.overlay {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.4); /* Dark overlay */
    color: white;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.slider-card:hover .overlay {
    opacity: 1;
    transition: opacity 0.5s ease-in-out;
}

.event-name {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 10px;
}

.event-date {
    font-size: 1.5rem;
    font-weight: normal;
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
        


        .select-button:hover {
            background-color: #0056b3; /* Optional: Hover effect */
        }
        #cancelModal .modal-content {
        background-color: #f9f9f9;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        border: none;
        }

        #cancelModal .modal-header {
            background-color: #0e2238;
            color: white;
            border-radius: 10px 10px 0 0;
        }

        #cancelModal .btn-close {
            filter: invert(1);
        }

        #cancelModal .modal-body {
            font-size: 1.1rem;
            color: #333;
            padding: 20px;
        }

        #cancelModal .modal-footer {
            justify-content: center;
            border-top: none;
        }

        #cancelModal .btn-secondary {
            background-color: #6c757d;
            border: none;
            transition: background-color 0.3s ease;
        }

        #cancelModal .btn-secondary:hover {
            background-color: #5a6268;
        }

        #cancelModal .btn-danger {
            background-color: #dc3545;
            border: none;
            transition: background-color 0.3s ease;
        }

        #cancelModal .btn-danger:hover {
            background-color: #c82333;
        }

      
        @media (min-width: 768px) {}
    </style>




<body>
    <div class="wrapper">
        <aside id="sidebar">
            <div class="d-flex">
                <button class="toggle-btn" type="button">
                    <i class="lni lni-grid-alt"></i>
                </button>
                <div class="sidebar-logo">
                    <a href="#"></br>Welcome!</br> <?php echo htmlspecialchars($adminDetails['fName'] . ' ' . $adminDetails['lName']); ?></a>
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
                    </a>
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




        <main class="mt-6 pt-3">
        
        <!-- Navbar
   <nav class="navbar navbar-expand-lg navbar-light bg-light">
       <div class="container">
           
           
           <form action="search.php" class="d-flex ms-auto">
              <button class="btn btn-outline-primary" type="submit">Search</button>
          </form>
       </div>
   </nav> -->
   <?php


// Fetch the registered events for the current user
$selectRegisteredEvents = "SELECT events.* FROM events 
                            JOIN event_registrations ON events.id = event_registrations.event_id 
                            WHERE event_registrations.member_email = '$email'";
$registeredEventsResult = mysqli_query($conn, $selectRegisteredEvents);

if ($registeredEventsResult && mysqli_num_rows($registeredEventsResult) > 0) {
    $registeredEvents = mysqli_fetch_all($registeredEventsResult, MYSQLI_ASSOC); // Fetch registered events
} else {
    $registeredEvents = []; // No registered events found
}
?>





    <div class="container mt-5">
        <h3 class="fw-bold fs-4 mb-3">Your Registered Events</h3>
        <hr/>
        
        <?php if (count($registeredEvents) > 0): ?>
            <div class="row">
                <?php foreach ($registeredEvents as $event): ?>
                    <div class="col-4 mb-3" id="event_<?= $event['id'] ?>">
                        <div class="card">
                            <img src="<?= htmlspecialchars($event['image_path']) ?>" alt="<?= htmlspecialchars($event['event_name']) ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($event['event_name']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($event['event_date']) ?></p>
                                <p class="card-text"><?= htmlspecialchars($event['description']) ?></p>
                                <button class="btn btn-danger" onclick="cancelRegistration(<?= $event['id'] ?>)">Cancel Registration</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>You have not registered for any events yet.</p>
        <?php endif; ?>
    </div>



    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalLabel">Confirm Cancellation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to cancel your registration for this event?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="confirmCancelButton">Confirm</button>
            </div>
        </div>
    </div>
</div>


    <script>
        function cancelRegistration(eventId) {
            // Show the cancellation confirmation modal
            const cancelModal = new bootstrap.Modal(document.getElementById('cancelModal'));
            cancelModal.show();

            // Set the confirmation button to cancel the registration when clicked
            document.getElementById('confirmCancelButton').onclick = function() {
                // Send an AJAX request to cancel the registration
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'cancel_registration.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            alert(response.message);
                            document.getElementById('event_' + eventId).remove(); // Remove the event from the list
                            cancelModal.hide(); // Hide the modal
                        } else {
                            alert(response.message);
                        }
                    }
                };
                xhr.send('event_id=' + eventId);
            };
        }
    </script>



   </div>
</section>


   <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
  

   <!-- VIEWING PART --> 
   <br>

   </main>

   <script>
  function showEventDetails(name, date, time, description) {
   // Set the modal content
   document.getElementById('eventName').innerText = name;
   document.getElementById('eventDate').innerText = date;
   document.getElementById('eventTime').innerText = time; // Ensure this gets the correct formatted time
   document.getElementById('eventDescription').innerText = description;

   // Show the modal
   const modal = new bootstrap.Modal(document.getElementById('eventDetailsModal'));
   modal.show();
}

console.log('Event Name:', name);
console.log('Event Date:', date);
console.log('Event Time:', time);
console.log('Event Description:', description);

   </script>
   


        <main class="mt-6 pt-3">

    <!-- offcanvas -->
    <main class="mt-6 pt-3">

    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    
    
                <!-- ADD HERE -->


    </main>

    <!-- Script Tags -->
    <script src="./adminjs/bootstrap.bundle.min.js"></script>
    <script src="./adminjs/jquery-3.5.1.js"></script>
    <script src="./adminjs/jquery.dataTables.min.js"></script>
    <script src="./adminjs/dataTables.bootstrap5.min.js"></script>

        </div>
    </div>
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