<?php
session_start();

$server = "localhost";
$user = "root";
$password = "";
$dbase = "fitzone";

$conn = mysqli_connect($server, $user, $password, $dbase);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $stmt = mysqli_prepare($conn, "SELECT email, fullname, password FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($pass, $row['password'])) {
            $_SESSION['email'] = $row['email'];
            header("Location: afterindex.php");
            exit();
        } else {
            $message = "Invalid password!";
        }
    } else {
        $message = "User not found!";
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | FitZone</title>
    <link rel="stylesheet" href="login.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    
</head>

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

<body>
    <div class="login-container">
        <form class="login-form" method="POST" action="">
            <h2>Login to FitZone</h2>
            
            <?php if (!empty($message)) echo "<div class='message'>$message</div>"; ?>

            <div class="input-box">
                <i class='bx bx-envelope'></i>
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-box">
                <i class='bx bx-lock-alt'></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="login-btn">Login</button>
            <p class="register-link">Don't have an account? <a href="sign up.php">Sign up</a></p>
            <p class="employer-link">Admin or Staff? <a href="employer_login.php">Employer Login</a></p>
        </form>
    </div>
</body>
</html>
