<?php
// PHP login script (login.php) - Links login.html to StaffUser table

// --- Database Connection Configuration ---
$servername = "localhost";
$username = "root";       
$password = "";           
$dbname = "ChapelSeminarDB"; 

// Check if the request method is POST (form submission)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get Form Data
    $email = trim($_POST['email'] ?? ''); 
    $password_input = $_POST['password'] ?? ''; 
    
    if (empty($email) || empty($password_input)) {
        display_error("Please enter both email and password.");
        exit;
    }

    // 1. Connect to Database
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        display_error("Database connection failed.");
        exit;
    }

    // 2. Query StaffUser Table for Hash
    $stmt = $conn->prepare("SELECT PasswordHash FROM StaffUser WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $stored_hash = $user['PasswordHash'];
        
        // 3. Verify the Password
        if (password_verify($password_input, $stored_hash)) {
            
            // SUCCESS: Redirect to seminar.html (The Dashboard)
            header("Location: seminar.html");
            exit; 

        } else {
            // Invalid password
            display_error("Invalid email or password.");
        }
    } else {
        // User not found
        display_error("Invalid email or password.");
    }

    $stmt->close();
    $conn->close();

} else {
    // Direct access redirect
    header("Location: login.html");
    exit;
}

function display_error($message) {
    echo "<!DOCTYPE html><html lang='en'><head><title>Login Failed</title></head><body>";
    echo "<h2>Login Failed</h2>";
    echo "<p>{$message} <a href='login.html'>Try again</a></p>";
    echo "</body></html>";
}
?>