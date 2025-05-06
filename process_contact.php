<?php
// Database connection parameters
$servername = "localhost";
$username = "root";  // Default XAMPP username
$password = "";      // Default XAMPP password
$dbname = "test_db";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if database exists, create if it doesn't
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) !== TRUE) {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($dbname);

// Check if table exists, create if it doesn't
$sql = "CREATE TABLE IF NOT EXISTS contact_messages (
    id INT(11) NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    submission_date DATETIME NOT NULL,
    PRIMARY KEY (id)
)";

if ($conn->query($sql) !== TRUE) {
    die("Error creating table: " . $conn->error);
}

// Validate form data
if (!isset($_POST['first_name']) || !isset($_POST['last_name']) || !isset($_POST['email']) || 
    !isset($_POST['subject']) || !isset($_POST['message'])) {
    header("Location: contact.html?status=error&message=missing_fields");
    exit();
}

// Get form data and sanitize inputs
$first_name = htmlspecialchars(trim($_POST['first_name']));
$last_name = htmlspecialchars(trim($_POST['last_name']));
$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
$phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
$subject = htmlspecialchars(trim($_POST['subject']));
$message = htmlspecialchars(trim($_POST['message']));
$submission_date = date('Y-m-d H:i:s');

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: contact.html?status=error&message=invalid_email");
    exit();
}

// Prepare SQL statement to prevent SQL injection
$stmt = $conn->prepare("INSERT INTO contact_messages (first_name, last_name, email, phone, subject, message, submission_date) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    // Log the error for debugging
    error_log("Prepare failed: " . $conn->error);
    header("Location: contact.html?status=error&message=db_error");
    exit();
}

$stmt->bind_param("sssssss", $first_name, $last_name, $email, $phone, $subject, $message, $submission_date);

// Execute the statement
if ($stmt->execute()) {
    // Redirect back to contact page with success message
    header("Location: contact.html?status=success");
    exit();
} else {
    // Log the error for debugging
    error_log("Execute failed: " . $stmt->error);
    header("Location: contact.html?status=error&message=insert_failed");
    exit();
}

// Close connection
$stmt->close();
$conn->close();
?>