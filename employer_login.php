<?php
session_start();

// Debugging: Log session start
file_put_contents('debug.log', "Session Start (employer_login.php): " . print_r($_SESSION, true) . "\n", FILE_APPEND);

// Define base URL for redirects
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/fitzone/";

$server = "localhost";
$user = "root";
$password = "";
$dbase = "fitzone";

$conn = mysqli_connect($server, $user, $password, $dbase);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$message = "";

// Hardcoded admin credentials
$admin_email = "admin123@gmail.com";
$admin_password = "admin123";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Log POST data
    file_put_contents('debug.log', "POST Data: " . print_r($_POST, true) . "\n", FILE_APPEND);

    $email = $_POST['email'] ?? '';
    $pass = $_POST['password'] ?? '';
    $login_type = $_POST['login_type'] ?? '';

    if (empty($email) || empty($pass) || empty($login_type)) {
        $message = "All fields are required!";
        file_put_contents('debug.log', "Error: Empty fields\n", FILE_APPEND);
    } elseif ($login_type === 'admin') {
        if ($email === $admin_email && $pass === $admin_password) {
            $_SESSION['email'] = $email;
            $_SESSION['role'] = 'admin';
            file_put_contents('debug.log', "Admin Login Success: Redirecting to admin.php\n", FILE_APPEND);
            header("Location: " . $base_url . "admin.php");
            exit();
        } else {
            $message = "Invalid admin email or password!";
            file_put_contents('debug.log', "Admin Login Failed: Invalid credentials\n", FILE_APPEND);
        }
    } elseif ($login_type === 'staff') {
        $stmt = mysqli_prepare($conn, "SELECT email, fullname, password FROM users WHERE email = ? AND role = 'staff'");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                if (password_verify($pass, $row['password'])) {
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['role'] = 'staff';
                    file_put_contents('debug.log', "Staff Login Success: Redirecting to staff.php\n", FILE_APPEND);
                    header("Location: " . $base_url . "staff.php");
                    exit();
                } else {
                    $message = "Invalid staff password!";
                    file_put_contents('debug.log', "Staff Login Failed: Invalid password\n", FILE_APPEND);
                }
            } else {
                $message = "Staff not found!";
                file_put_contents('debug.log', "Staff Login Failed: Staff not found\n", FILE_APPEND);
            }
            mysqli_stmt_close($stmt);
        } else {
            $message = "Database error!";
            file_put_contents('debug.log', "Staff Login Failed: Query error\n", FILE_APPEND);
        }
    }
} else {
    file_put_contents('debug.log', "Not a POST request\n", FILE_APPEND);
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin & Staff Login | FitZone</title>
    <link rel="stylesheet" href="employer_login.css?v=<?php echo time(); ?>">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <h2>Admin or Staff Login</h2>
        <?php if (!empty($message)): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <!-- Admin Login Form -->
        <form class="login-form" method="POST" action="">
            <h3>Admin</h3>
            <input type="hidden" name="login_type" value="admin">
            <div class="input-box">
                <i class='bx bx-envelope'></i>
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-box">
                <i class='bx bx-lock-alt'></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="login-btn">Login as Admin</button>
        </form>

        <!-- Staff Login Form -->
        <form class="login-form" method="POST" action="">
            <h3>Staff</h3>
            <input type="hidden" name="login_type" value="staff">
            <div class="input-box">
                <i class='bx bx-envelope'></i>
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-box">
                <i class='bx bx-lock-alt'></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="login-btn">Login as Staff</button>
        </form>

        <p class="register-link">Back to <a href="login.php">Main Login</a></p>
    </div>
</body>
</html>