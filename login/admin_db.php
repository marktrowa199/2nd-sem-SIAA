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
    
    <title>Admin Dashboard</title>
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
         <div class="main">
        <nav class="navbar navbar-expand px-4 py-3">
            <form action="search.php" class="d-flex ms-auto">
                <input type="text" class="form-control me-2" placeholder="Search..." aria-label="Search">
                <button class="btn btn-outline-primary" type="submit">Search</button>
            </form>
            
        </nav>  
            <main class="content px-3 py-4">
                <div class="container-fluid">
                    <div class="mb-3">
                        
                        <h3 class="fw-bold fs-4 mb-3">Dashboard</h3>
                        <hr/>
                    </div>
                    <div class="row g-3">
                    </div>
                </div>
            </main>

   
 <div class="offcanvas offcanvas-start sidebar-nav bg-dark" tabindex="-1" id="sidebar">
        <div class="offcanvas-body p-0">
            <nav class="navbar-dark">
                <ul class="navbar-nav">
                    <li>
                        <div class="text-muted small fw-bold text-uppercase px-3">CORE</div>
                    </li>
                    <li>
                        <a href="#" class="nav-link px-3 active">
                            <span class="me-2"><i class="bi bi-speedometer2"></i></span>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="my-4"><hr class="dropdown-divider bg-light" /></li>
                    <li>
                        <div class="text-muted small fw-bold text-uppercase px-3 mb-3">Interface</div>
                    </li>
                    
                    <li>
                        <a href="#" class="nav-link px-3">
                            <span class="me-2"><i class="bi bi-book-fill"></i></span>
                            <span>Pages</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="nav-link px-3">
                            <span class="me-2"><i class="bi bi-book-fill"></i></span>
                            <span>FAQ page</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="nav-link px-3">
                            <span class="me-2"><i class="bi bi-person-lines-fill"></i></span>
                            <span>Profile Form</span>
                        </a>
                    </li>
                    <li class="my-4"><hr class="dropdown-divider bg-light" /></li>
                    <li>
                        <div class="text-muted small fw-bold text-uppercase px-3 mb-3">Addons</div>
                    </li>
                    <li>
                        <a href="#" class="nav-link px-3">
                            <span class="me-2"><i class="bi bi-graph-up"></i></span>
                            <span>Charts</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="nav-link px-3">
                            <span class="me-2"><i class="bi bi-table"></i></span>
                            <span>Tables</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <!-- offcanvas -->
    <main class="mt-6 pt-3">
        <div class="container-fluid">
            <!-- Additional Row for Charts -->
            <div class="row justify-content-center">
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-header">People participating per event</div>
                        <div class="card-body">
                            <canvas id="eventParticipantsChart" width="400" height="200"></canvas>

                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-header">People participating per month</div>
                        <div class="card-body">
                            <canvas id="monthlyParticipationChart" width="400" height="200"></canvas> <!-- Moved here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lower Row with More Cards -->
            <div class="row justify-content-center">
            <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-header"><h3>Manage your events!</h3></div>
                        <div class="card-body">
                            <?php if (!empty($events)): ?>
                                <ul class="list-group">
                                    <?php foreach ($events as $event): ?>
                                        <li class="list-group-item">
                                            <strong><?php echo htmlspecialchars($event['event_name']); ?></strong><br>
                                            Date: <?php echo date("F j, Y", strtotime($event['event_date'])); ?><br>
                                            Time: <?php echo date("h:i A", strtotime($event['event_time'])); ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p>No upcoming events found.</p>
                            <?php endif; ?>
                            <br>
                            <a href="manage_db.php" class="btn btn-primary">Manage Events</a>
                        </div>
                    </div>
                    
                </div>

                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-header"><h3>View your events!</h3></div>
                        <div class="card-body">
                            <p>Total number of events: <br><h1><strong><?php echo $totalEvents; ?></strong></h1></p>
                            <br>
                            <br>
                            <br>
                            <br>
                            <a href="view_db.php" class="btn btn-primary">View All Events</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
   
        
   


    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>

    </script>
    
                <!-- ADD HERE -->


    </main>

    
    <!-- Script Tags -->
    <script src="./adminjs/bootstrap.bundle.min.js"></script>
    <script src="./adminjs/jquery-3.5.1.js"></script>
    <script src="./adminjs/jquery.dataTables.min.js"></script>
    <script src="./adminjs/dataTables.bootstrap5.min.js"></script>
    
    <!-- script for per event -->
    <script> 
        // Use AJAX to fetch the participant data from the database
    fetch('fetch_participant_data.php')
        .then(response => response.json())
        .then(data => {
            const eventNames = [];
            const participantCounts = [];

            // Process the data to extract event names and participant counts
            data.forEach(event => {
                eventNames.push(event.event_name);
                participantCounts.push(event.participant_count);
            });

            // Create the chart using the extracted data
            const ctx = document.getElementById('eventParticipantsChart').getContext('2d');
            const eventParticipantsChart = new Chart(ctx, {
                type: 'bar', // Bar chart
                data: {
                    labels: eventNames, // Event names as labels
                    datasets: [{
                        label: 'Participants per Event',
                        data: participantCounts, // Participant counts
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error fetching participant data:', error));
    </script>

<!-- SCRIPT FOR PER MONTH -->
<script>
     // Use AJAX to fetch the participant data grouped by month
     fetch('fetch_participant_data_by_month.php') // Replace with the correct file if it's different
        .then(response => response.json())
        .then(data => {
            const months = [];
            const participantCounts = [];

            // Process the data to extract months and participant counts
            data.forEach(entry => {
                // Convert 'YYYY-MM' to 3-letter month abbreviation (e.g., 'Jan', 'Feb')
                const month = new Date(entry.month + '-01'); // Adding the first day of the month
                const monthAbbr = month.toLocaleString('default', { month: 'short' }); // Get 3-letter abbreviation
                months.push(monthAbbr); // Push the 3-letter month abbreviation
                participantCounts.push(entry.participant_count); // Participant count
            });

            // Create the chart using the extracted data
            const ctx = document.getElementById('monthlyParticipationChart').getContext('2d');
            const monthlyParticipationChart = new Chart(ctx, {
                type: 'bar', // Bar chart type
                data: {
                    labels: months, // Months as labels (now 3-letter abbreviations)
                    datasets: [{
                        label: 'Participants per Month',
                        data: participantCounts, // Participant counts for each month
                        backgroundColor: 'rgba(75, 192, 192, 0.2)', // Light color for the bars
                        borderColor: 'rgba(75, 192, 192, 1)', // Darker color for the border of the bars
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true, // Ensures the y-axis starts at 0
                            ticks: {
                                stepSize: 1 // Ensures there are steps in the y-axis ticks
                            }
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error fetching monthly participant data:', error));
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