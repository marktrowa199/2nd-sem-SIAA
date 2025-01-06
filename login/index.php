<?php
session_start();
include 'connect.php';

if (isset($_POST['submit'])) {
    // Login functionality
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $select = "SELECT * FROM user_form WHERE email = '$email' AND password = '$password'";
    $result = mysqli_query($conn, $select);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);

        if ($row['user_type'] == 'Admin') {
            $_SESSION['admin_name'] = $row['fName'];
            $_SESSION['admin_email'] = $row['email'];
            header("location:admin_db.php");
            exit();
        } elseif ($row['user_type'] == 'Member') {
            $_SESSION['user_name'] = $row['fName'];
            $_SESSION['user_email'] = $row['email'];
            header("location:memberdb.php");
            exit();
        }
    } else {
        $error[] = 'Incorrect email or password!';
    }
}

if (isset($_POST['forgot_password'])) {
    // Forgot Password functionality
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $select = "SELECT * FROM user_form WHERE email = '$email'";
    $result = mysqli_query($conn, $select);

    if (mysqli_num_rows($result) > 0) {
        $show_reset_password = true;
        $_SESSION['reset_email'] = $email;
    } else {
        $error[] = 'Email not found!';
    }
}

if (isset($_POST['reset_password'])) {
    // Reset Password functionality
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $email = $_SESSION['reset_email'];

    $update = "UPDATE user_form SET password = '$new_password' WHERE email = '$email'";
    if (mysqli_query($conn, $update)) {
        unset($_SESSION['reset_email']);
        $success = 'Password successfully updated!';
    } else {
        $error[] = 'Failed to update password!';
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
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="row vh-100 g-0">
        <!-- Left Side -->
        <div class="col-lg-6 position-relative d-none d-lg-block">
            <div class="bg-holder" style="background-image: url('../image/crocs.png');"></div>
        </div>
        <!--/ Left Side -->

        <!-- Right Side -->
        <div class="col-lg-6">
            <div class="row align-items-center justify-content-center h-100 g-0 px-4 px-sm-0">
                <div class="col col-sm-6 col-lg-7 col-xl-6">
                    <!-- Logo -->
                    <a href="#" class="d-flex justify-content-center mb-4">
                        <img src="../image/logo.png">
                    </a>
                    <!--/ Logo -->

                    <div class="text-center mb-5">
                        <h3 class="fw-bold">Log-In</h3>
                        <p class="text-secondary">Get access to your account</p>
                    </div>

                    <!-- Form -->
                    <form action="index.php" method="POST">
                            <?php
                            if (isset($error)) {
                                foreach ($error as $err) {
                                    echo '<span class="error-msg">'.$err.'</span>';
                                }
                            }
                            if (isset($success)) {
                                echo '<span class="success-msg">'.$success.'</span>';
                            }
                            ?>

                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class='bx bx-user'></i></span>
                                <input type="text" name="email" class="form-control form-control-lg" placeholder="Email" required>
                            </div>

                            <?php if (isset($show_reset_password) && $show_reset_password): ?>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class='bx bx-lock'></i></span>
                                    <input type="password" name="new_password" class="form-control form-control-lg" placeholder="New Password" required>
                                </div>
                                <button type="submit" name="reset_password" class="btn btn-success btn-lg w-100">Reset Password</button>
                            <?php else: ?>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class='bx bx-lock'></i></span>
                                    <input type="password" name="password" id="password" class="form-control form-control-lg" placeholder="Password" required>
                                    <span class="input-group-text" onclick="togglePasswordVisibility()">
                                        <i class='bx bx-show' id="togglePasswordIcon"></i>
                                    </span>
                                </div>

                                <button type="submit" name="submit" class="btn btn-primary btn-lg w-100">Login</button>
                                <br>
                                <button type="submit" name="forgot_password" class="btn btn-link">Forgot Password?</button>
                                
                                
                            <?php endif; ?>
                        </form>

                    <div class="text-center mt-3">
                        <small>Don't have an account? <a href="registration.php" class="fw-bold">Sign Up</a></small>
                    </div>
                    <!--/ Form -->
                </div>
            </div>
        </div>
        <!--/ Right Side -->
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- show pass -->
    <script>
    function togglePasswordVisibility() {
        const passwordField = document.getElementById("password");
        const toggleIcon = document.getElementById("togglePasswordIcon");
        
        if (passwordField.type === "password") {
            passwordField.type = "text";
            toggleIcon.classList.replace("bx-show", "bx-hide");
        } else {
            passwordField.type = "password";
            toggleIcon.classList.replace("bx-hide", "bx-show");
        }
    }
</script>


</body>
</html>
