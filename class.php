<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$server = "localhost";
$user = "root";
$password = "";
$dbase = "fitzone";

$conn = mysqli_connect($server, $user, $password, $dbase);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_email = $_SESSION['email'];
$error = $success = "";

// Check if tables exist
if (!mysqli_query($conn, "DESCRIBE class")) {
    $error = "Error: The 'class' table does not exist in the 'fitzone' database. Please create it.";
}
if (!mysqli_query($conn, "DESCRIBE appointments")) {
    $error = "Error: The 'appointments' table does not exist in the 'fitzone' database. Please create it.";
}
if (!mysqli_query($conn, "DESCRIBE contact_messages")) {
    $error = "Error: The 'contact_messages' table does not exist in the 'fitzone' database. Please create it.";
}

if (empty($error)) {
    // Handle appointment form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['appointment_submit'])) {
        $purpose = trim($_POST['class_name'] ?? '');
        $day = trim($_POST['day'] ?? '');
        $time = trim($_POST['time'] ?? '');
        $trainer_email = trim($_POST['trainer'] ?? '');

        if (empty($purpose) || empty($day) || empty($time)) {
            $error = "Class Name, Day, and Time are required for appointment.";
        } else {
            // Validate trainer_email
            if (!empty($trainer_email)) {
                if (!filter_var($trainer_email, FILTER_VALIDATE_EMAIL)) {
                    $error = "Invalid trainer email format.";
                } else {
                    $stmt = mysqli_prepare($conn, "SELECT email FROM trainers WHERE email = ?");
                    mysqli_stmt_bind_param($stmt, "s", $trainer_email);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    if (mysqli_num_rows($result) == 0) {
                        $error = "Trainer email does not exist in the system.";
                        $trainer_email = null;
                    }
                    mysqli_stmt_close($stmt);
                }
            } else {
                $trainer_email = null;
            }

            if (empty($error)) {
                $appointment_date = date('Y-m-d H:i:s', strtotime("$day $time"));
                $status = 'pending';

                $stmt = mysqli_prepare($conn, "INSERT INTO appointments (customer_email, trainer_email, appointment_date, purpose, status) VALUES (?, ?, ?, ?, ?)");
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "sssss", $user_email, $trainer_email, $appointment_date, $purpose, $status);
                    if (mysqli_stmt_execute($stmt)) {
                        $success = "Appointment booked successfully!";
                    } else {
                        $error = "Error booking appointment: " . mysqli_stmt_error($stmt);
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $error = "Prepare failed: " . mysqli_error($conn);
                }
            }
        }
    }

    // Handle timetable form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['timetable_submit'])) {
        $class_name = trim($_POST['class_name'] ?? '');
        $day = trim($_POST['day'] ?? '');
        $time = trim($_POST['time'] ?? '');
        $duration = trim($_POST['duration'] ?? '');
        $trainer = trim($_POST['trainer'] ?? '');

        if (empty($class_name) || empty($day) || empty($time) || empty($duration)) {
            $error = "All fields are required for timetable.";
        } elseif (!preg_match("/^\d+$/", $duration) || $duration < 15 || $duration > 180) {
            $error = "Duration must be a number between 15 and 180 minutes.";
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO class (user_email, type, class_name, day, time, duration, trainer) VALUES (?, 'timetable', ?, ?, ?, ?, ?)");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssssis", $user_email, $class_name, $day, $time, $duration, $trainer);
                if (mysqli_stmt_execute($stmt)) {
                    $success = "Class added to timetable successfully!";
                } else {
                    $error = "Error adding class: " . mysqli_stmt_error($stmt);
                }
                mysqli_stmt_close($stmt);
            } else {
                $error = "Prepare failed: " . mysqli_error($conn);
            }
        }
    }

    // Fetch schedules and timetables
    $classes = [];
    $result = mysqli_query($conn, "SELECT class_name, day, time, duration, trainer FROM class WHERE type IN ('schedule', 'timetable') ORDER BY day, time");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $classes[] = $row;
        }
        mysqli_free_result($result);
    } else {
        $error = "Error fetching classes: " . mysqli_error($conn);
    }

    // Fetch user’s appointments
    $appointments = [];
    $stmt = mysqli_prepare($conn, "SELECT purpose AS class_name, appointment_date, trainer_email AS trainer FROM appointments WHERE customer_email = ? ORDER BY appointment_date");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $user_email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $row['day'] = date('l', strtotime($row['appointment_date']));
            $row['time'] = date('H:i', strtotime($row['appointment_date']));
            $appointments[] = $row;
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Error fetching appointments: " . mysqli_error($conn);
    }

    // Fetch user’s messages and replies
    $messages = [];
    $stmt = mysqli_prepare($conn, "SELECT message_text, reply_text, sent_at, replied_at FROM contact_messages WHERE customer_email = ? AND reply_text IS NOT NULL ORDER BY replied_at DESC");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $user_email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $messages[] = $row;
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Error fetching messages: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitZone Class Management</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="class.css">
</head>
<body>
    <header>
        <a href="afterindex.php" class="logo">FitZone Fitness <span>Center</span></a>
        <div class="top-btn">
            <a href="afterindex.php" class="nav-btn">Back to Home</a>
        </div>
    </header>

    <section class="class-section">
        <div class="class-container">
            <h2>Gym Schedule & Timetable</h2>
            <?php if ($error): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <?php if ($success): ?>
                <p class="success"><?php echo htmlspecialchars($success); ?></p>
            <?php endif; ?>
            <?php if (empty($classes)): ?>
                <p>No classes scheduled yet.</p>
            <?php else: ?>
                <table class="schedule-table">
                    <thead>
                        <tr>
                            <th>Class Name</th>
                            <th>Day</th>
                            <th>Time</th>
                            <th>Duration</th>
                            <th>Trainer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($classes as $class): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($class['class_name']); ?></td>
                                <td><?php echo htmlspecialchars($class['day']); ?></td>
                                <td><?php echo htmlspecialchars($class['time']); ?></td>
                                <td><?php echo htmlspecialchars($class['duration']); ?> min</td>
                                <td><?php echo htmlspecialchars($class['trainer'] ?? 'N/A'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <h2>Your Appointments</h2>
            <?php if (empty($appointments)): ?>
                <p>No appointments booked yet.</p>
            <?php else: ?>
                <table class="schedule-table">
                    <thead>
                        <tr>
                            <th>Class Name</th>
                            <th>Day</th>
                            <th>Time</th>
                            <th>Trainer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appt): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($appt['class_name']); ?></td>
                                <td><?php echo htmlspecialchars($appt['day']); ?></td>
                                <td><?php echo htmlspecialchars($appt['time']); ?></td>
                                <td><?php echo htmlspecialchars($appt['trainer'] ?? 'N/A'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <h2>Your Messages</h2>
            <?php if (empty($messages)): ?>
                <p>No replies from staff yet.</p>
            <?php else: ?>
                <table class="message-table">
                    <thead>
                        <tr>
                            <th>Your Message</th>
                            <th>Staff Reply</th>
                            <th>Sent At</th>
                            <th>Replied At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $msg): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($msg['message_text']); ?></td>
                                <td><?php echo htmlspecialchars($msg['reply_text']); ?></td>
                                <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($msg['sent_at']))); ?></td>
                                <td><?php echo htmlspecialchars($msg['replied_at'] ? date('Y-m-d H:i', strtotime($msg['replied_at'])) : 'N/A'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <h2>Book an Appointment</h2>
            <form class="class-form" action="Class.php" method="post">
                <input type="text" name="class_name" placeholder="Class Name (e.g., Yoga)" required>
                <select name="day" required>
                    <option value="">Select Day</option>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                    <option value="Saturday">Saturday</option>
                    <option value="Sunday">Sunday</option>
                </select>
                <input type="time" name="time" required>
                <input type="text" name="trainer" placeholder="Trainer Email (optional, e.g., mike@fitzone.com)">
                <input type="hidden" name="appointment_submit" value="1">
                <input type="submit" value="Book Appointment" class="class-btn">
            </form>

            <h2>Add New Class to Timetable</h2>
            <form class="class-form" action="Class.php" method="post">
                <input type="text" name="class_name" placeholder="Class Name (e.g., HIIT)" required>
                <select name="day" required>
                    <option value="">Select Day</option>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                    <option value="Saturday">Saturday</option>
                    <option value="Sunday">Sunday</option>
                </select>
                <input type="time" name="time" required>
                <input type="number" name="duration" placeholder="Duration (minutes)" required>
                <input type="text" name="trainer" placeholder="Trainer (optional)">
                <input type="hidden" name="timetable_submit" value="1">
                <input type="submit" value="Add Class" class="class-btn">
            </form>
        </div>
    </section>
</body>
</html>