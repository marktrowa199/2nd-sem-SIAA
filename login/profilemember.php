<?php
session_start();
include 'connect.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user session is set
if (!isset($_SESSION['user_email'])) {
    header("location:index.php");
    exit();
}

// Retrieve the user email from the session
$email = $_SESSION['user_email'];

// Prepare and execute the SQL query to fetch member details from the database
$stmt = $conn->prepare("SELECT * FROM user_form WHERE email = ? AND user_type = 'Member'");
$stmt->bind_param("s", $_SESSION['user_email']);
$stmt->execute();
$resultMember = $stmt->get_result();

// Fetch updated details
if ($resultMember && $resultMember->num_rows > 0) {
    $memberDetails = $resultMember->fetch_assoc();

     // Initialize member variables
     $memberName = htmlspecialchars(trim($memberDetails['fName'] ?? '') . ' ' . trim($memberDetails['lName'] ?? 'Guest'));
     $memberEmail = htmlspecialchars($memberDetails['email'] ?? 'No Email');
     $profilePicPath = $memberDetails['profile_pic'] ?? 'uploads/default-profile.png';
     $about = $memberDetails['about'] ?? '';
} else {
    echo "Error fetching user details.";
     // Optionally set fallback values
     $memberName = 'Guest';
     $memberEmail = 'No Email';
     $profilePicPath = 'uploads/default-profile.png';
     $about = '';
}
$conn->close(); // Close the database connection
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #eef2f3;
            color: #333;
        }
        .navbar {
            background: #4b6f9e;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .profile-card {
            background-color: #ffffff; 
            border-radius: 15px; 
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); 
            transition: transform 0.3s; 
        }
        .profile-card:hover {
            transform: translateY(-5px); 
        }
        .profile-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
        }
        .member-icon {
            font-size: 6rem; 
            color: #007bff; 
        }
        .btn-primary {
            background-color: #0056b3; 
            border: none;
        }
        .btn-primary{
            background-color: #0056b3; 
        }
        .info-section {
            background-color: white; 
            padding: 20px;
            border-radius: 15px;
            margin-top: 20px; 
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg shadow-sm">
        <div class="container">
            <a href="memberdb.php" class="btn btn-link">
                <i class="fas fa-arrow-left text-white" style="font-size: 1.5rem;"></i> 
            </a>
            <div class="ms-auto">
                <a href="logout.php" class="btn btn-outline-secondary">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row d-flex justify-content-center">
            <div class="col-md-8">
                <div class="profile-card p-4">
                    <div class="text-center">
                        <img src="<?php echo htmlspecialchars($profilePicPath); ?>" alt="Profile Picture" class="rounded-circle profile-img" />
                        <h2 class="font-weight-bold mt-3"><?php echo htmlspecialchars($memberName); ?></h2>
                        <p class="text-muted"><?php echo htmlspecialchars($memberEmail); ?></p>
                        <p class="text-muted">Member</p>
                        <p class="mt-4"><?php echo htmlspecialchars($about); ?></p>
                        
                    </div>
                </div>
                <div class="info-section mt-4">
                    <h3>Information</h3>
                    <div class="d-flex flex-column align-items-start">
                        <a href="editprofilemember.php" class="btn btn-link">Edit Profile</a>
                        <hr class="w-100">
                        <a href="#" class="btn btn-link">Review Events by You</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>