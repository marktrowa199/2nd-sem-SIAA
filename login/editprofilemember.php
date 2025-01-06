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
if (!$stmt) {
    die("Prepare failed: " . $conn->error); // Check if the statement was prepared successfully
}

$stmt->bind_param("s", $email); // Bind the email parameter
if (!$stmt->execute()) {
    die("Query failed: " . $stmt->error); // Check if the query was executed successfully
}

$resultMember = $stmt->get_result(); // Get the result of the query

// Check if the member details were retrieved successfully
if ($resultMember && $resultMember->num_rows > 0) {
    $memberDetails = $resultMember->fetch_assoc(); // Fetch user details as an associative array

    // Initialize the member variables with fallback values
    $memberName = htmlspecialchars(trim($memberDetails['fName'] ?? '') . ' ' . trim($memberDetails['lName'] ?? 'Guest'));
    $memberEmail = htmlspecialchars($memberDetails['email'] ?? 'No Email');
    $profilePicPath = $memberDetails['profile_pic'] ?? 'uploads/default-profile.png';
    $about = $memberDetails['about'] ?? '';
} else {
    die("Member details not found.");
}

// Handle form submission for updating the profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $about = $_POST['about'] ?? ''; // Get the "About" text
    $newEmail = $_POST['email'] ?? ''; // Get the new email
    
    // Handle file upload for the new profile picture
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($_FILES['photo']['name']);
        
        // Move the uploaded file to the uploads directory
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile)) {
            $profilePicPath = $uploadFile; // Update profile picture path if upload is successful
        } else {
            echo "Error uploading file.";
            $profilePicPath = $memberDetails['profile_pic'] ?? 'uploads/default-profile.png';
        }
    }

    // Prepare and execute the update SQL query
    $stmtUpdate = $conn->prepare("UPDATE user_form SET email = ?, about = ?, profile_pic = ? WHERE email = ?");
    $stmtUpdate->bind_param("ssss", $newEmail, $about, $profilePicPath, $email);
    if ($stmtUpdate->execute()) {
        // Redirect to the profile page to reflect changes
        $_SESSION['user_email'] = $newEmail;
        header("Location: profilemember.php");
        exit();
    } else {
        echo "Error updating profile: " . $stmtUpdate->error;
    }

    $stmtUpdate->close();
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        body {
            background-color: #f0f4f8;
        }

        .navbar {
            background: #4b6f9e;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .profile-card {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-top: 2rem;
        }

        .profile-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
        }

        .btn-primary {
            background-color: #4A90E2;
            border: none;
        }

        .btn-primary:hover {
            background-color: #357ABD;
        }

        .btn-secondary {
            background-color: #E74C3C;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #C0392B;
        }

        .info-section {
            background-color: #eef3f8;
            padding: 1.5rem;
            border-radius: 10px;
            margin-top: 1.5rem;
        }

        h3 {
            color: #34495E;
        }

        label {
            color: #2C3E50;
        }

        input,
        textarea {
            border: 1px solid #d1d3d4;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a href="profilemember.php" class="btn btn-link text-white">
                <i class="fas fa-arrow-left" style="font-size: 1.5rem;"></i>
            </a>
            <div class="ms-auto">
                <a href="logout.php" class="btn btn-outline-secondary">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="profile-card text-center">
                    <img id="profileImg" src="<?php echo $profilePicPath; ?>" alt="Profile Picture" class="rounded-circle profile-img" />
                    <h2 class="font-weight-bold mt-3"><?php echo htmlspecialchars($memberName); ?></h2>
                    <p class="text-muted">Member</p>

                    <form action="editprofilemember.php" method="post" enctype="multipart/form-data">
                        <input type="file" name="photo" id="photoInput" accept="image/*" style="display: none;">
                        <button class="btn btn-primary mt-3 mb-3" type="button" onclick="document.getElementById('photoInput').click();">Upload Photo</button>
                        
                        <div class="mb-3">
                            <label for="about" class="form-label">About</label>
                            <textarea class="form-control" name="about" id="about" rows="4"><?php echo htmlspecialchars($about ?? ''); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="email" value="<?php echo htmlspecialchars($memberEmail); ?>" required>
                        </div>

                        <div class="text-end">
                            <button type="reset" class="btn btn-secondary me-2">Cancel</button>
                            <button type="submit" name="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('photoInput').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profileImg').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>