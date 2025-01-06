<?php
@include 'connect.php'; // Include the database connection file

if (isset($_POST['submit'])) {
    // Retrieve form inputs and sanitize
    $fName = mysqli_real_escape_string($conn, $_POST['fName']);
    $lName = mysqli_real_escape_string($conn, $_POST['lName']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $birthdate = mysqli_real_escape_string($conn, $_POST['birthdate']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $user_type = 'Member'; // Set default user type as Member

    // Calculate age from birthdate
    $dob = new DateTime($birthdate);
    $today = new DateTime();
    $age = $today->diff($dob)->y; // Get the year difference as age

    // Check if the user already exists
    $select = "SELECT * FROM user_form WHERE email = '$email'";
    $result = mysqli_query($conn, $select);

    if (mysqli_num_rows($result) > 0) {
        $error[] = 'User already exists!'; // User already exists error
    } else {
        // Insert new user into database
        $insert = "INSERT INTO user_form (fName, lName, email, password, birthdate, age, gender, user_type) VALUES ('$fName','$lName','$email','$password','$birthdate','$age','$gender','$user_type')";
        if (mysqli_query($conn, $insert)) {
            // Redirect to login page after successful registration
            header('location:index.php');
            exit();
        } else {
            // Handle insert failure
            $_SESSION['error_message'] = "Registration failed. Please try again.";
            header("Location: registration.php");
            exit();
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LocalVista</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="registration.css">
</head>

<body>
    <div class="row vh-100 g-0">
        <!-- Left Side -->
        <div class="col-lg-6 position-relative d-none d-lg-block">
            <div class="bg-holder"></div>
        </div>
        <!--/ Left Side -->

        <!-- Right Side -->
        <div class="col-lg-6">
            <div class="row align-items-center justify-content-center h-100 g-0 px-4 px-sm-0">
                <div class="col col-sm-6 col-lg-7 col-xl-6">
                    <!-- Logo -->
                    <a href="#" class="d-flex justify-content-center mb-4">
                        <img src="../image/logo.png" alt="" width="200">
                    </a>
                    <!--/ Logo -->

                    <div class="text-center mb-5">
                        <h3 class="fw-bold">Register</h3>

                        <!-- Form -->
                        <form id="registrationForm" action="registration.php" method="POST">
                            <?php
                            if (isset($error)) {
                                foreach ($error as $err) {
                                    echo '<span class="error-msg">' . $err . '</span>';
                                }
                            }
                            ?>
                            <div class="input-group mb-3">
                                <span class="input-group-text">
                                    <i class='bx bx-user'></i>
                                </span>
                                <input type="text" name="fName" class="form-control form-control-lg fs-6" placeholder="First Name" required>
                            </div>

                            <div class="input-group mb-3">
                                <span class="input-group-text">
                                    <i class='bx bx-user'></i>
                                </span>
                                <input type="text" name="lName" class="form-control form-control-lg fs-6" placeholder="Last Name" required>
                            </div>

                            <div class="input-group mb-3">
                                <span class="input-group-text">
                                    <i class='bx bx-envelope'></i>
                                </span>
                                <input type="email" name="email" class="form-control form-control-lg fs-6" placeholder="Email" required>
                            </div>

                            <div class="input-group mb-3">
                                <span class="input-group-text">
                                    <i class='bx bx-lock-alt'></i>
                                </span>
                                <!-- Add id="password" to this field -->
                                <input type="password" id="password" name="password" class="form-control form-control-lg fs-6" placeholder="Password" required>
                                <span class="input-group-text" onclick="togglePasswordVisibility()">
                                    <i class='bx bx-show' id="togglePasswordIcon"></i>
                                </span>
                            </div>
                            <!-- here -->
                            <div class="input-group mb-3">
                                <span class="input-group-text">
                                    <i class='bx bx-calendar'></i>
                                </span>
                                <input type="date" name="birthdate" class="form-control form-control-lg fs-6" placeholder="Birthdate" required>
                            </div>

                            <div class="input-group mb-3">
                                <span class="input-group-text">
                                    <i class='bx bx-male-female'></i>
                                </span>
                                <select name="gender" class="form-select form-select-lg fs-6" required>
                                    <option value="" disabled selected>Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>


                            <!-- Terms and Conditions Checkbox -->
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="termsCheck" required>
                                <label class="form-check-label" for="termsCheck">
                                    Yes, I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">terms and conditions</a>
                                </label>
                            </div>

                            <button type="submit" name="submit" class="btn btn-primary btn-lg w-100 mb-3">Register as Member</button>
                        </form>
                        <!--/ Form -->

                        <div class="text-center mb-4">
                            <small>Already Have Account? <a href="index.php" class="fw-bold">Sign In</a></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Right Side -->
    </div>

    <!-- Terms and Conditions Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">Terms and Conditions of LocalVista Church</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Effective Date:</strong> October 15, 2024</p>
                    <p>Welcome to LocalVista Church! By attending services, participating in events, or using our services, you agree to these Terms and Conditions. If you do not agree, please refrain from participating.</p>

                    <h6>1. Acceptance of Terms</h6>
                    <p>By accessing or using the services of LocalVista Church, you agree to be bound by these Terms and Conditions and our Privacy Policy. If you do not agree with any part of these terms, you must not use our services.</p>

                    <h6>2. Conduct</h6>
                    <p>All attendees are expected to behave respectfully and kindly toward others. Disruptive behavior, harassment, or discrimination will not be tolerated and may result in removal from church activities.</p>

                    <h6>3. Membership</h6>
                    <p>Membership at LocalVista Church is open to all individuals who share our vision and mission. Members are encouraged to participate in church activities and events.</p>

                    <h6>4. Privacy</h6>
                    <p>Your privacy is important to us. We will collect, use, and protect your personal information in accordance with our Privacy Policy. By using our services, you consent to our collection and use of your information as described.</p>

                    <h6>5. Events and Activities</h6>
                    <p>LocalVista Church organizes various events and activities, which may include religious services, community outreach, and social gatherings. Participants are responsible for their own conduct during these events.</p>

                    <h6>6. Media Release</h6>
                    <p>By attending events, you consent to the recording, photographing, and/or filming of yourself, and you grant LocalVista Church the right to use such recordings for promotional purposes.</p>

                    <h6>7. Liability Waiver</h6>
                    <p>Participants agree to release LocalVista Church, its leaders, and members from any liability for any injury, loss, or damage sustained during church activities or events.</p>

                    <h6>8. Code of Conduct</h6>
                    <p>Attendees should dress modestly and respectfully for all church functions. Mobile devices should be silenced during services and events.</p>

                    <h6>9. Modifications to Terms</h6>
                    <p>LocalVista Church reserves the right to modify these Terms and Conditions at any time. Any changes will be effective upon posting on our website. Your continued use of our services after changes indicates your acceptance of the new terms.</p>

                    <h6>10. Contact Information</h6>
                    <p>For any questions or concerns regarding these Terms and Conditions, please contact us at:</p>
                    <p>LocalVista Church<br>Telephone No.:<br>09123456789<br>test@gmail.com</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function togglePasswordVisibility() {
    const passwordField = document.getElementById("password"); // Target the password field
    const toggleIcon = document.getElementById("togglePasswordIcon"); // Target the icon
    
    if (passwordField.type === "password") {
        passwordField.type = "text"; // Change the input type to text (show password)
        toggleIcon.classList.replace("bx-show", "bx-hide"); // Change the icon to a "hide" icon
    } else {
        passwordField.type = "password"; // Change the input type to password (hide password)
        toggleIcon.classList.replace("bx-hide", "bx-show"); // Change the icon to a "show" icon
    }
}

</script>

</body>

</html>
