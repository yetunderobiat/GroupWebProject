<?php
// register.php - Links signin.html to the StaffUser table

// --- Database Connection Configuration ---
$servername = "localhost";
$username = "root";       
$password = "";           
$dbname = "ChapelSeminarDB"; 

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Collect and Validate Form Data
    $fullName = trim($_POST['fullName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $staffID = trim($_POST['staffID'] ?? '');
    $faculty = trim($_POST['faculty'] ?? '');
    $password_input = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Simple validation (must match the signin.html form)
    if (empty($fullName) || empty($email) || empty($staffID) || empty($faculty) || empty($password_input) || empty($confirmPassword)) {
        display_error("Please fill in all required fields.");
        exit;
    }

    if ($password_input !== $confirmPassword) {
        display_error("Passwords do not match.");
        exit;
    }
    
    // 2. Securely Hash the Password
    $passwordHash = password_hash($password_input, PASSWORD_DEFAULT);

    // 3. Connect to Database
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        display_error("Database connection failed: " . $conn->connect_error);
        exit;
    }

    // 4. INSERT INTO StaffUser Table
    try {
        $stmt = $conn->prepare("INSERT INTO StaffUser (FullName, Email, StaffID, Faculty, PasswordHash) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $fullName, $email, $staffID, $faculty, $passwordHash);

        if ($stmt->execute()) {
            // SUCCESS: Redirect to login.html
            echo "<script>alert('Account created successfully! You can now log in.'); window.location.href='login.html';</script>";
        } else {
             // Handles duplicate Email or Staff ID (Error 1062)
            if ($conn->errno == 1062) {
                display_error("Error: The Email or Staff ID already exists. Try logging in.");
            } else {
                display_error("Database Error: Could not register user.");
            }
        }

        $stmt->close();
        
    } catch (Exception $e) {
        display_error("An unexpected error occurred.");
    } finally {
        $conn->close();
    }
} else {
    // Direct access redirect
    header("Location: signin.html");
    exit;
}

function display_error($message) {
    echo "<!DOCTYPE html><html><body>";
    echo "<h1>Registration Failed</h1>";
    echo "<p>{$message} <a href='signin.html'>Try again</a></p>";
    echo "</body></html>";
}
?>