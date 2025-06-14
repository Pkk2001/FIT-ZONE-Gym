<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$server = "localhost";
$user = "root";
$password = "";
$dbase = "fitzone";

$conn = mysqli_connect($server, $user, $password, $dbase);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $plan = $_POST['plan'] ?? '';

    // Validate inputs
    if (empty($fullname) || empty($email) || empty($phone) || empty($plan)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    }  else {
        // Insert data into plans table
        $stmt = mysqli_prepare($conn, "INSERT INTO plans (fullname, email, phone, plan) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssss", $fullname, $email, $phone, $plan);
            if (mysqli_stmt_execute($stmt)) {
                $success = "Registration successful! Welcome to FitZone.";
            } else {
                $error = "Error saving data: " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        } else {
            $error = "Prepare failed: " . mysqli_error($conn);
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Join FitZone</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="join.css">
</head>
<body>
    <section class="join-section">
        <div class="join-container">
            <h2>Join FitZone Today</h2>
            <?php if (isset($error)): ?>
                <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
            <?php endif; ?>
            <form class="join-form" action="join.php" method="post">
                <input type="text" name="fullname" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email Address" required>
                <input type="tel" name="phone" placeholder="Phone Number" required>
                <select name="plan" required>
                    <option value="">Select a Plan</option>
                    <option value="basic">Basic - $10/month</option>
                    <option value="standard">Standard - $20/month</option>
                    <option value="premium">Premium - $30/month</option>
                </select>
                <input type="submit" value="Register Now" class="join-btn">
            </form>
        </div>
    </section>
</body>
</html>