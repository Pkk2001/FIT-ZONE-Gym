<?php 
session_start();
include('dbconnect.php');
error_reporting(0);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    if ($password !== $cpassword) {
        $error = "Passwords do not match!";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $checkEmail = "SELECT * FROM users WHERE email='$email'";
        $result = $conn->query($checkEmail);

        if ($result->num_rows > 0) {
            $error = "Email already exists!";
        } else {
            $sql = "INSERT INTO users (fullname, email, password) VALUES ('$fullname', '$email', '$hashedPassword')";
            if ($conn->query($sql) === TRUE) {
                $success = "Registration successful!";
            } else {
                $error = "Error: " . $conn->error;
            }
        }
    }

    $conn->close();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up | FitZone</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="sign up.css">
</head>
<body>
    <div class="signup-container">
        <form class="signup-form" action="" method="POST">
            <h2>Sign Up</h2>

            <?php if ($error) echo "<div class='message'>$error</div>"; ?>
            <?php if ($success) echo "<div class='message success'>$success</div>"; ?>

            <div class="input-box">
                <i class='bx bx-user'></i>
                <input type="text" name="fullname" placeholder="Full Name" required>
            </div>

            <div class="input-box">
                <i class='bx bx-envelope'></i>
                <input type="email" name="email" placeholder="Email Address" required>
            </div>

            <div class="input-box">
                <i class='bx bx-lock-alt'></i>
                <input type="password" name="password" placeholder="Create Password" required>
            </div>

            <div class="input-box">
                <i class='bx bx-lock'></i>
                <input type="password" name="cpassword" placeholder="Confirm Password" required>
            </div>

            <button type="submit" class="signup-btn">Sign Up</button>

            <p class="login-link">Already have an account? <a href="login.php">Login</a></p>
        </form>
    </div>
</body>
</html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Background Image Example</title>
    <style>
        /*Background Image*/
        body {
            background-image: url('img/background.jpg');
            background-size: cover;
            background-position: center; 
            background-repeat: no-repeat; 
            background-attachment: fixed; 
            height: 100vh;
            width: 100vw;
            margin: 0;
            overflow-x: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            color: black; 
            
        }

        
        .content {
            background: rgba(0, 0, 0, 0.5); 
            padding: 20px;
            border-radius: 10px;
        }
    </style>
</head>