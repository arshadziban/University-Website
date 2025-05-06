<?php
// Database connection parameters
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password is empty
$dbname = "test_db";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) !== TRUE) {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($dbname);

// Create simple_applications table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS simple_applications (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    firstName VARCHAR(50) NOT NULL,
    lastName VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    dob DATE NOT NULL,
    gender VARCHAR(10) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(50) NOT NULL,
    state VARCHAR(50) NOT NULL,
    zip VARCHAR(20) NOT NULL,
    country VARCHAR(50) NOT NULL,
    program VARCHAR(50) NOT NULL,
    major VARCHAR(100) NOT NULL,
    education VARCHAR(50) NOT NULL,
    gpa VARCHAR(10),
    statement TEXT,
    terms BOOLEAN NOT NULL,
    application_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) !== TRUE) {
    die("Error creating table: " . $conn->error);
}

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data and sanitize inputs
    $firstName = $conn->real_escape_string($_POST['firstName']);
    $lastName = $conn->real_escape_string($_POST['lastName']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $dob = $conn->real_escape_string($_POST['dob']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $address = $conn->real_escape_string($_POST['address']);
    $city = $conn->real_escape_string($_POST['city']);
    $state = $conn->real_escape_string($_POST['state']);
    $zip = $conn->real_escape_string($_POST['zip']);
    $country = $conn->real_escape_string($_POST['country']);
    $program = $conn->real_escape_string($_POST['program']);
    $major = $conn->real_escape_string($_POST['major']);
    $education = $conn->real_escape_string($_POST['education']);
    $gpa = isset($_POST['gpa']) ? $conn->real_escape_string($_POST['gpa']) : '';
    $statement = isset($_POST['statement']) ? $conn->real_escape_string($_POST['statement']) : '';
    $terms = isset($_POST['terms']) ? 1 : 0;

    // Insert data into the simple_applications table
    $sql = "INSERT INTO simple_applications (firstName, lastName, email, phone, dob, gender, address, city, state, zip, country, program, major, education, gpa, statement, terms)
    VALUES ('$firstName', '$lastName', '$email', '$phone', '$dob', '$gender', '$address', '$city', '$state', '$zip', '$country', '$program', '$major', '$education', '$gpa', '$statement', '$terms')";

    if ($conn->query($sql) === TRUE) {
        // Redirect to a thank you page
        header("Location: application_success.html");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>