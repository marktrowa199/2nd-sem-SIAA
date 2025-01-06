<?php
session_start();

include 'connect.php';

// Check if the admin session is set
if (!isset($_SESSION['admin_name']) || !isset($_SESSION['admin_email'])) {
    header("location:index.php");
    exit();
}

// Retrieve the admin email from the session
$email = $_SESSION['admin_email'];

// Fetch both fName and lName from the user_form table
$selectAdmin = "SELECT fName, lName, email FROM user_form WHERE email = '$email' AND user_type = 'Admin'";
$resultAdmin = mysqli_query($conn, $selectAdmin);

if ($resultAdmin && mysqli_num_rows($resultAdmin) > 0) {
    $adminDetails = mysqli_fetch_assoc($resultAdmin);
} else {
    die("Admin details not found.");
}

// Combine fName and lName to display the full name
$adminName = isset($adminDetails['fName']) && isset($adminDetails['lName']) ? htmlspecialchars($adminDetails['fName']) . ' ' . htmlspecialchars($adminDetails['lName']) : 'Unknown Admin';

// Query to fetch upcoming events ordered by date and time
$upcomingEventsQuery = $conn->prepare("SELECT * FROM events WHERE event_date >= NOW() ORDER BY event_date ASC LIMIT 4");
$upcomingEventsQuery->execute();
$resultEvents = $upcomingEventsQuery->get_result();

$events = [];
if ($resultEvents && $resultEvents->num_rows > 0) {
    $events = $resultEvents->fetch_all(MYSQLI_ASSOC); // Fetch events as an associative array
}

// Query to count the total number of events
$totalEventsQuery = $conn->prepare("SELECT COUNT(*) as total FROM events");
$totalEventsQuery->execute();
$resultTotalEvents = $totalEventsQuery->get_result();

$totalEvents = 0; // Default to zero if the query fails
if ($resultTotalEvents && $resultTotalEvents->num_rows > 0) {
    $totalRow = $resultTotalEvents->fetch_assoc();
    $totalEvents = $totalRow['total']; // Get the total count
}
// Query to count the total number of admins
$totalAdminsQuery = $conn->prepare("SELECT COUNT(*) as total FROM user_form WHERE user_type = 'Admin'");
$totalAdminsQuery->execute();
$resultTotalAdmins = $totalAdminsQuery->get_result();

$totalAdmins = 0; // Default to zero if the query fails
if ($resultTotalAdmins && $resultTotalAdmins->num_rows > 0) {
    $totalRow = $resultTotalAdmins->fetch_assoc();
    $totalAdmins = $totalRow['total']; // Get the total count of admins
}

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background-color: #f0f4f8; /* Soft light background color */
            font-family: 'Arial', sans-serif;
        }

        .navbar {
            background-color: #4b6f9e; /* Soft dark blue background for navbar */
        }

        .navbar-brand,
        .nav-link {
            color: #ffffff !important; /* White color for navbar items */
        }

        .bg-card {
            background-color: #ffffff; /* White background for card */
            color: #333; /* Dark text color for better contrast */
            border-radius: 1rem; /* Rounded corners */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); /* Soft shadow */
        }

        .user-icon {
            color: #4b6f9e; /* Icon color matching navbar */
        }

        h2 {
            font-size: 2rem; /* Increased font size for admin name */
            font-weight: bold; /* Bold font weight */
        }

        h5 {
            font-size: 1.2rem; /* Adjusted size for subheadings */
            margin-bottom: 0.5rem; /* Spacing below */
        }

        p {
            font-size: 1rem; /* Standard font size */
        }

        /* Media query for responsiveness */
        @media (max-width: 768px) {
            .container {
                padding: 1rem; /* Adjust padding on smaller screens */
            }

            h2 {
                font-size: 1.8rem; /* Smaller font size on mobile */
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="admin_db.php">BACK</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                </ul>
                <a href="logout.php" class="btn btn-outline-light">Logout</a> <!-- Logout button -->
            </div>
        </div>
    </nav>

    <!-- Main content -->
    <div class="container mt-5">
        <div class="row d-flex justify-content-center">
            <div class="col-md-10">
                <div class="bg-card p-4"> <!-- Card with padding -->
                    <div class="row">
                        <div class="col-md-8"> <!-- Left side for information -->
                            <div class="row text-center">
                                <div class="col">
                                    <h5><i class="fas fa-users-cog"></i> Total Admins:</h5>
                                    <p><?php echo $totalAdmins; ?> Admins</p> <!-- Dynamic count of admins -->
                                </div>
                                <div class="col">
                                    <h5><i class="fas fa-user-tie"></i> Active Admin:</h5>
                                    <p><?php echo $adminName; ?></p> <!-- Display the concatenated name -->
                                </div>
                                <div class="col">
                                    <h5><i class="fas fa-calendar-alt"></i> Last Login:</h5>
                                    <p><?php echo date("F j, Y"); ?></p> <!-- Dynamic date -->
                                </div>
                                <div class="col">
                                    <h5><i class="fas fa-envelope"></i> Contact Email:</h5>
                                    <p><?php echo htmlspecialchars($adminDetails['email']); ?></p> <!-- Dynamic data -->
                                </div>
                            </div>
                            <div class="text-center mt-4">
                                <p><strong>Identity Verification</strong></p>
                                <p>At LocalVista, we prioritize your security and privacy. Our identity verification process ensures that all user information 
                                    is handled with the utmost care and in compliance with data protection regulations. 
                                    We use advanced technologies to authenticate your identity, safeguarding your account from unauthorized access and maintaining the integrity of our community.</p>
                            </div>
                        </div>
                        <div class="col-md-4 text-center"> <!-- Right side for admin name -->
                            <i class="fas fa-user-tie user-icon mt-3" style="font-size: 5rem;"></i>
                            <h2 class="mt-4"><?php echo $adminName; ?></h2> <!-- Dynamic admin name -->
                            <p>Admin</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>



