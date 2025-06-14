<?php
session_start();

// Debugging
file_put_contents('debug.log', "Admin.php Session: " . print_r($_SESSION, true) . "\n", FILE_APPEND);

// Define base URL
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/fitzone/";

// Check if user is admin
if (!isset($_SESSION['email']) || $_SESSION['email'] !== 'admin123@gmail.com' || $_SESSION['role'] !== 'admin') {
    file_put_contents('debug.log', "Admin.php: Unauthorized, redirecting to login.php\n", FILE_APPEND);
    header("Location: " . $base_url . "login.php");
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

// Handle logout
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: " . $base_url . "login.php");
    exit();
}

// Handle delete customer
if (isset($_POST['delete_customer'])) {
    $email = $_POST['email'];
    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE email = ? AND role = 'customer'");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// Handle add staff
$message = "";
if (isset($_POST['add_staff'])) {
    $email = $_POST['staff_email'];
    $fullname = $_POST['staff_name'];
    $password = password_hash($_POST['staff_password'], PASSWORD_DEFAULT);

    $stmt = mysqli_prepare($conn, "INSERT INTO users (email, fullname, password, role) VALUES (?, ?, ?, 'staff')");
    mysqli_stmt_bind_param($stmt, "sss", $email, $fullname, $password);
    if (mysqli_stmt_execute($stmt)) {
        $message = "Staff added successfully!";
    } else {
        $message = "Error adding staff: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}

// Handle add trainer
if (isset($_POST['add_trainer'])) {
    $name = $_POST['trainer_name'];
    $email = $_POST['trainer_email'];
    $specialty = $_POST['trainer_specialty'];

    $stmt = mysqli_prepare($conn, "INSERT INTO trainers (name, email, specialty) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sss", $name, $email, $specialty);
    if (mysqli_stmt_execute($stmt)) {
        $message = "Trainer added successfully!";
    } else {
        $message = "Error adding trainer: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}

// Handle edit trainer
if (isset($_POST['edit_trainer'])) {
    $id = $_POST['trainer_id'];
    $name = $_POST['trainer_name'];
    $email = $_POST['trainer_email'];
    $specialty = $_POST['trainer_specialty'];

    $stmt = mysqli_prepare($conn, "UPDATE trainers SET name = ?, email = ?, specialty = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "sssi", $name, $email, $specialty, $id);
    if (mysqli_stmt_execute($stmt)) {
        $message = "Trainer updated successfully!";
    } else {
        $message = "Error updating trainer: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}

// Handle delete trainer
if (isset($_POST['delete_trainer'])) {
    $id = $_POST['trainer_id'];
    $stmt = mysqli_prepare($conn, "DELETE FROM trainers WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// Fetch customers
$customers = [];
$result = mysqli_query($conn, "SELECT email, fullname FROM users WHERE role = 'customer'");
while ($row = mysqli_fetch_assoc($result)) {
    $customers[] = $row;
}

// Fetch trainers
$trainers = [];
$result = mysqli_query($conn, "SELECT id, name, email, specialty FROM trainers");
while ($row = mysqli_fetch_assoc($result)) {
    $trainers[] = $row;
}

// Fetch plans
$plans = [];
$result = mysqli_query($conn, "SELECT id, fullname, email, phone, plan, created_at FROM plans ORDER BY created_at DESC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $plans[] = $row;
    }
    mysqli_free_result($result);
} else {
    $message = "Error fetching plans: " . mysqli_error($conn);
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | FitZone</title>
    <link rel="stylesheet" href="admin.css?v=<?php echo time(); ?>">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <h2>Admin Dashboard</h2>
        <?php if (!empty($message)): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <!-- Customers -->
        <div class="admin-section">
            <h3>Customers</h3>
            <?php if (empty($customers)): ?>
                <p>No customers found.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                <td><?php echo htmlspecialchars($customer['fullname']); ?></td>
                                <td>
                                    <form method="POST" action="">
                                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>">
                                        <button type="submit" name="delete_customer" class="delete-btn">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Add Staff -->
        <div class="admin-section">
            <h3>Add Staff</h3>
            <form method="POST" action="" class="admin-form">
                <div class="input-box">
                    <i class='bx bx-envelope'></i>
                    <input type="email" name="staff_email" placeholder="Staff Email" required>
                </div>
                <div class="input-box">
                    <i class='bx bx-user'></i>
                    <input type="text" name="staff_name" placeholder="Staff Name" required>
                </div>
                <div class="input-box">
                    <i class='bx bx-lock-alt'></i>
                    <input type="password" name="staff_password" placeholder="Password" required>
                </div>
                <button type="submit" name="add_staff" class="submit-btn">Add Staff</button>
            </form>
        </div>

        <!-- Manage Trainers -->
        <div class="admin-section">
            <h3>Gym Trainers</h3>
            <!-- Add Trainer -->
            <form method="POST" action="" class="admin-form">
                <h4>Add Trainer</h4>
                <div class="input-box">
                    <i class='bx bx-user'></i>
                    <input type="text" name="trainer_name" placeholder="Trainer Name" required>
                </div>
                <div class="input-box">
                    <i class='bx bx-envelope'></i>
                    <input type="email" name="trainer_email" placeholder="Trainer Email" required>
                </div>
                <div class="input-box">
                    <i class='bx bx-dumbbell'></i>
                    <input type="text" name="trainer_specialty" placeholder="Specialty (e.g., Yoga)" required>
                </div>
                <button type="submit" name="add_trainer" class="submit-btn">Add Trainer</button>
            </form>

            <!-- Trainers List -->
            <?php if (empty($trainers)): ?>
                <p>No trainers found.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Specialty</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($trainers as $trainer): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($trainer['name']); ?></td>
                                <td><?php echo htmlspecialchars($trainer['email']); ?></td>
                                <td><?php echo htmlspecialchars($trainer['specialty']); ?></td>
                                <td>
                                    <form method="POST" action="" class="inline-form">
                                        <input type="hidden" name="trainer_id" value="<?php echo $trainer['id']; ?>">
                                        <input type="text" name="trainer_name" value="<?php echo htmlspecialchars($trainer['name']); ?>" required>
                                        <input type="email" name="trainer_email" value="<?php echo htmlspecialchars($trainer['email']); ?>" required>
                                        <input type="text" name="trainer_specialty" value="<?php echo htmlspecialchars($trainer['specialty']); ?>" required>
                                        <button type="submit" name="edit_trainer" class="edit-btn">Edit</button>
                                        <button type="submit" name="delete_trainer" class="delete-btn">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Membership Plans -->
        <div class="admin-section">
            <h3>Membership Plans</h3>
            <?php if (empty($plans)): ?>
                <p>No plans found.</p>
            <?php else: ?>
                <table class="plans-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Plan</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($plans as $plan): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($plan['id']); ?></td>
                                <td><?php echo htmlspecialchars($plan['fullname']); ?></td>
                                <td><?php echo htmlspecialchars($plan['email']); ?></td>
                                <td><?php echo htmlspecialchars($plan['phone']); ?></td>
                                <td><?php echo htmlspecialchars($plan['plan']); ?></td>
                                <td><?php echo htmlspecialchars($plan['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <form method="POST" action="">
            <button type="submit" name="logout" class="logout-btn">Logout</button>
        </form>
        <p class="back-link">Back to <a href="afterindex.php">Home</a></p>
    </div>
</body>
</html>