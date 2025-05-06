<?php
session_start();

// Database connection parameters
$serverName = "localhost"; 
$username = "root";     // Your MySQL username
$dbPassword = "";       // Your MySQL password appears to be empty based on the phpMyAdmin screenshot
$database = "test_db";    // Your database name

// Function to validate user credentials
function validateUser($email, $password, $conn) {
    // Prepare SQL statement to prevent SQL injection
    $stmt = mysqli_prepare($conn, "SELECT id, email, password, full_name FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    
    // Debug: Check if email exists
    if ($result && mysqli_num_rows($result) == 0) {
        return array('success' => false, 'error' => 'email_not_found');
    }
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        // Plain text password comparison instead of hashing
        if ($password !== $row['password']) {
            return array('success' => false, 'error' => 'password_mismatch');
        }
        
        // If passwords match
        if ($password === $row['password']) {
            return array(
                'success' => true,
                'user_id' => $row['id'],
                'email' => $row['email'],
                'full_name' => $row['full_name']
            );
        }
    }
    
    return array('success' => false);
}

// Process login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $email = $_POST['email'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? true : false;
    
    // Connect to database using mysqli
    $conn = mysqli_connect($serverName, $username, $dbPassword, $database);
    
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    // Validate user credentials
    $result = validateUser($email, $password, $conn);
    
    if ($result['success']) {
        // Set session variables
        $_SESSION['user_id'] = $result['user_id'];
        $_SESSION['email'] = $result['email'];
        $_SESSION['full_name'] = $result['full_name'];
        $_SESSION['logged_in'] = true;
        
        // Set remember me cookie if requested (30 days)
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            setcookie('remember_token', $token, time() + (86400 * 30), "/");
            
            // Store token in database
            $stmt = mysqli_prepare($conn, "INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
            $expires_at = date('Y-m-d H:i:s', time() + (86400 * 30));
            mysqli_stmt_bind_param($stmt, "iss", $result['user_id'], $token, $expires_at);
            mysqli_stmt_execute($stmt);
        }
        
        // Close the database connection before redirecting
        mysqli_close($conn);
        
        // Redirect to index.html after successful login
        header("Location: profile.html");
        exit();
    } else {
        // Debug information
        if (isset($result['error'])) {
            if ($result['error'] == 'email_not_found') {
                header("Location: profile.html?error=email_not_found");
            } else if ($result['error'] == 'password_mismatch') {
                // For security in production, you would not expose this detail
                // This is only for debugging purposes
                header("Location: profile.html?error=password_mismatch");
            } else {
                header("Location: profile.htmll?error=invalid_credentials");
            }
        } else {
            header("Location: profile.html?error=invalid_credentials");
        }
        
        // Close the database connection before redirecting
        mysqli_close($conn);
        exit();
    }
}
?>