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
    
    <title>Member Dashboard</title>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            margin-bottom: 50px;
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
    width: 95%; /* Adjust the width */
    height: 400px; /* Set a default height */
    margin: 20px auto; /* Center and add space between cards */
    border-radius: 10px;
    overflow: hidden; /* Ensure no overflow outside the card */
    transition: transform 0.5s ease, box-shadow 0.3s ease; /* Add smooth transition for hover effect */
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

.slider-card:hover {
    transform: translateY(-10px); /* Move the card slightly up on hover */
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3); /* Add deeper shadow for hover */
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

/* Adjusting the carousel */
.owl-carousel .owl-item {
    display: flex;
    justify-content: center;
    align-items: center;
    transition: transform 0.5s ease-in-out;
}

.owl-carousel {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
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
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
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

        .main {
            margin: left 70px;
            transition: margin-left .25s ease-in-out;
        }

        #sidebar.expand +.main {
            margin: left 260px;
            
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
        transform: translateY(30px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
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
        
        /* carousel button*/
        .select-button {
    margin-bottom: 20px; /* Adjust the value as needed */
    padding: 10px 20px;  /* Optional: Add padding for a more contained look */
    background-color: #007bff; /* Optional: Button color */
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.select-button:hover {
    background-color: #0056b3; /* Optional: Hover effect */
}

#description {
    background: #0e2238; /* Updated to use #184e77 */
    color: #f0f0f0; /* Light Gray for text */
    padding: 40px 20px;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); /* Subtle shadow for depth */
}

.cta-link {
    background-color: #ffcc5c; /* Soft Gold */
    color: #0e2238; /* Deep Navy Blue for text */
    padding: 10px 20px;
    font-size: 1.2rem;
    font-weight: 700;
    text-decoration: none;
    border-radius: 8px;
    display: inline-block;
    margin-top: 20px;
    transition: all 0.3s ease;
}

#description p {
    font-size: 1.2rem;
    line-height: 1.6;
    color: #555;
    color: white;
}

#description ul {
    list-style-type: disc;
    padding-left: 20px;
    color: #555;
}

#description ul li {
    font-size: 1.2rem;
}
.container {
    max-width: 800px;
    margin: 0 auto;
}

.heading {
    font-size: 2.5rem;
    font-weight: 700;
    color: #FF5733;
    animation: fadeIn 2s ease-in-out;
}

.intro {
    font-size: 1.2rem;
    font-weight: 500;
    color: #333;
    line-height: 1.6;
    margin-bottom: 30px;
}

.highlight {
    font-size: 1.1rem;
    font-weight: 600;
    color: #FF5733;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.highlight-text {
    color: #FF7F50; /* A warmer, softer yellow */
    font-weight: 700;
}


.events-list {
    list-style: none;
    padding: 0;
    margin-bottom: 30px;
}

.events-list li {
    font-size: 1.1rem;
    margin: 15px 0;
    font-weight: 500;
    color: #555;
    line-height: 1.8;
    animation: slideIn 1s ease-out;
}

@keyframes slideIn {
    0% {
        opacity: 0;
        transform: translateX(-50px);
    }
    100% {
        opacity: 1;
        transform: translateX(0);
    }
}

.cta {
    font-size: 1.3rem;
    font-weight: 600;
    color: #333;
    margin-top: 40px;
    text-transform: uppercase;
}

.cta .highlight-text {
    color: #FF5733;
    font-weight: 700;
}

/* Button-like effect on the CTA */
.cta:hover {
    color: #FF5733;
    cursor: pointer;
    text-decoration: underline;
}

.cta-link:hover {
    background-color: #3a7bd5; /* Teal/Blue for hover effect */
    color: #ffffff; /* White text on hover */
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

/* About Us Section */
.about-us {
    background-color: #f5f5f5; /* Light gray background */
    padding: 30px 20px;
    margin-top: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    position: relative;
}

.about-us .container {
    max-width: 800px;
    margin: 0 auto;
}

.about-title {
    font-size: 2rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 15px;
}

.about-description, .about-goals {
    font-size: 1rem;
    color: #555;
    line-height: 1.6;
    margin-bottom: 20px;
}

.about-description {
    font-weight: 400;
}

.about-goals {
    font-weight: 600;
}

/* Place the About Us section at the bottom of the slider-card */
.slider-card {
    position: relative;
    width: 85%;
    height: 400px; /* Adjust the height */
    margin: 20px auto;
    border-radius: 10px;
    overflow: hidden;
    transition: transform 0.5s ease, box-shadow 0.3s ease;
}

.slider-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
}

/* Make sure the About Us section doesn't overlap on smaller screens */
@media (max-width: 768px) {
    .about-us {
        padding: 20px;
    }
    .about-title {
        font-size: 1.5rem;
    }
    .about-description, .about-goals {
        font-size: 0.9rem;
    }
}

      
        @media (min-width: 768px) {}



.dashboard-main {
  position: relative;
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: url("../image/church-background.jpeg") no-repeat center center / cover;
  color: #fff;
  text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.8);
}

/* Overlay for better text visibility */
.dashboard-main .overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.6);
  z-index: 1;
}

/* Content Styling */
.dashboard-main .content {
  position: relative;
  z-index: 2;
  max-width: 800px;
  text-align: center;
}

.dashboard-main h1 {
  font-size: 3rem;
  font-weight: bold;
}

.dashboard-main p {
  font-size: 1.25rem;
  margin-bottom: 1.5rem;
}

.dashboard-main .btn-primary {
  background-color: #007bff;
  border: none;
  padding: 10px 20px;
  font-size: 1.25rem;
  transition: background-color 0.3s ease;
}

.dashboard-main .btn-primary:hover {
  background-color: #0056b3;
}
 
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
                <li class="sidebar-item">
                <a href="logout.php" class="sidebar-link">
                    <i class="lni lni-exit"></i>
                    <span>Logout</span>
                </a>
                
                </li>
            </ul>
       
        </aside>
        <div class="main">

   



        <main class="mt-6 pt-0">
    
<section>
  <main class="dashboard-main">
    <div class="overlay"></div>
    <div class="content text-center">
      <h1 class="mb-4">Welcome LocalVista</h1>
      <p class="lead mb-5">“For where two or three gather in my name, there am I with them.” – Matthew 18:20</p>
      <a href="viewME.php" class="btn btn-lg btn-primary">Let's Get Started</a>
    </div>
</section>


<section id="slider" class="pt-5">
    <hr/>
   <div class="container">
       <h1 class="text"><b>What's happening</b></h1>
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
                           // Format the event date
                           $formattedDate = date("F j, Y", strtotime($event['event_date']));
                           
                           echo '<div class="slider-card">';
                           echo '    <div class="image-container" style="background-image: url(\'' . htmlspecialchars($event['image_path']) . '\');">';
                           echo '        <div class="overlay">';
                           echo '            <h5 class="event-name">' . htmlspecialchars($event['event_name']) . '</h5>';
                           echo '            <p class="event-date">' . $formattedDate . '</p>';
                           echo '        </div>';
                           echo '    </div>';
                           echo '</div>';
                       }
                   } else {
                       echo '<p>Error fetching events: ' . mysqli_error($conn) . '</p>';
                   }

                   // Close the database connection
                   mysqli_close($conn);
               ?>
           </div>
       </div>
   </div>

   <div class="about-us">
    <div class="container">
        <h2 class="about-title">About Us</h2>
        <p class="about-description">
            LocalVista is a community-driven platform designed to bring people closer by showcasing local events, activities, and gatherings. Our goal is to help users discover and participate in events within their community, creating a vibrant space for social interaction, learning, and entertainment.
        </p>
        <p class="about-goals">
            Our mission is to provide an easy-to-use platform where individuals can explore, register, and engage with events that interest them. We strive to foster stronger connections within neighborhoods and local communities, enriching the social and cultural fabric of society.
        </p>
    </div>
</div>


</section>










   <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- FOR CAROUSEL -->
  <script>
$(document).ready(function(){
    $(".owl-carousel").owlCarousel({
        loop: true,
        margin: 10,
        nav: true,
        autoplay: true,
        autoplayTimeout: 4000,  // Adjusted for smoother transition (default 3500)
        autoplayHoverPause: true,  // Pauses the autoplay when hovered for better user experience
        items: 1,  // Show 1 item at a time
        center: true,  // Center the item
        navText: [
            "<i class='fa fa-angle-left'></i>",
            "<i class='fa fa-angle-right'></i>"
        ],
        smartSpeed: 800, // Smoother transition between slides (default 250ms)
        responsive: {
            0: { items: 1 },  // 1 image on small screens
            600: { items: 1 },  // 1 image on medium screens
            1000: { items: 1 }  // 1 image on large screens
        }
    });
});


   </script>
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