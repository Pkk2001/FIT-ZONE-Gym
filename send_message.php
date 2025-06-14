<?php
// send_message.php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email   = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Database connection
    $conn = new mysqli("localhost", "root", "", "fitzone");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Validate email exists in users table
    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        header("Location: contact.php?error=Invalid email. Please use a registered email.");
        $stmt->close();
        $conn->close();
        exit();
    }
    $stmt->close();

    // Insert message into contact_messages
    $stmt = $conn->prepare("INSERT INTO contact_messages (customer_email, message_text) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $message);

    if ($stmt->execute()) {
        header("Location: contact.php?success=1");
    } else {
        header("Location: contact.php?error=Failed to send message: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: contact.php");
}
?>