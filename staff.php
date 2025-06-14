<?php
session_start();

$base_url = "http://localhost/fitzone/";

if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    header("Location: " . $base_url . "login.php");
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

$message = "";

// Handle message reply
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reply_message'])) {
    $message_id = $_POST['message_id'];
    $reply_text = $_POST['reply_text'];
    $staff_email = $_SESSION['email'];

    $stmt = mysqli_prepare($conn, "UPDATE contact_messages SET reply_text = ?, staff_email = ?, replied_at = NOW() WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ssi", $reply_text, $staff_email, $message_id);
    if (mysqli_stmt_execute($stmt)) {
        $message = "Reply sent successfully!";
    } else {
        $message = "Error sending reply: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}

// Fetch appointments
$appointments = [];
$query = "
    SELECT a.id, a.customer_email, u.fullname, a.trainer_email, t.name AS trainer_name, a.appointment_date, a.purpose, a.status
    FROM appointments a
    JOIN users u ON a.customer_email = u.email
    LEFT JOIN trainers t ON a.trainer_email = t.email
    WHERE u.role = 'customer'
";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $appointments[] = $row;
    }
    mysqli_free_result($result);
} else {
    $message = "Appointments query failed: " . mysqli_error($conn);
}

// Fetch contact messages
$contact_messages = [];
$query = "
    SELECT cm.id, cm.customer_email, u.fullname, cm.message_text, cm.reply_text, cm.sent_at, cm.replied_at
    FROM contact_messages cm
    JOIN users u ON cm.customer_email = u.email
    WHERE u.role = 'customer'
";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $contact_messages[] = $row;
    }
    mysqli_free_result($result);
} else {
    $message = "Messages query failed: " . mysqli_error($conn);
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard | FitZone</title>
    <link rel="stylesheet" href="staff.css?v=<?php echo time(); ?>">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
    <div class="staff-container">
        <h2>Staff Dashboard</h2>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?></p>

        <?php if (!empty($message)): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="staff-section">
            <h3>Customer Appointments</h3>
            <?php if (empty($appointments)): ?>
                <p>No appointments found.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Trainer</th>
                            <th>Date & Time</th>
                            <th>Purpose</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appt): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($appt['fullname']); ?> (<?php echo htmlspecialchars($appt['customer_email']); ?>)</td>
                                <td><?php echo htmlspecialchars($appt['trainer_name'] ?? 'None'); ?></td>
                                <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($appt['appointment_date']))); ?></td>
                                <td><?php echo htmlspecialchars($appt['purpose']); ?></td>
                                <td><?php echo htmlspecialchars($appt['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="staff-section">
            <h3>Customer Messages</h3>
            <?php if (empty($contact_messages)): ?>
                <p>No messages found.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Message</th>
                            <th>Sent At</th>
                            <th>Reply</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contact_messages as $msg): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($msg['fullname']); ?> (<?php echo htmlspecialchars($msg['customer_email']); ?>)</td>
                                <td><?php echo htmlspecialchars($msg['message_text']); ?></td>
                                <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($msg['sent_at']))); ?></td>
                                <td><?php echo htmlspecialchars($msg['reply_text'] ?? 'No reply yet'); ?></td>
                                <td>
                                    <?php if (empty($msg['reply_text'])): ?>
                                        <form method="POST" action="">
                                            <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                            <textarea name="reply_text" placeholder="Type your reply" required></textarea>
                                            <button type="submit" name="reply_message" class="reply-btn">Send Reply</button>
                                        </form>
                                    <?php else: ?>
                                        <span>Replied</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <form method="POST" action="">
        <a href="login.php" class="logout-btn">Logout</a>
            
        </form>
        <p class="back-link">Back to <a href="index.html">Home</a></p>
    </div>
</body>
</html>