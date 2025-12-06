<?php
// delete_student.php - Handles deletion of a student

// 1. Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ChapelSeminarDB";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. Check if an ID was passed in the URL (e.g., delete_student.php?id=5)
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']); // Clean the input to be an integer

    // 3. Prepare the Delete Command
    $stmt = $conn->prepare("DELETE FROM Student WHERE StudentID = ?");
    $stmt->bind_param("i", $id);

    // 4. Execute and Redirect
    if ($stmt->execute()) {
        // Success: Show popup and go back
        echo "<script>
                alert('Student successfully deleted.');
                window.location.href = 'roster_view.php';
              </script>";
    } else {
        // Database Error
        echo "<script>
                alert('Error: Could not delete student. " . $conn->error . "');
                window.location.href = 'roster_view.php';
              </script>";
    }
    $stmt->close();

} else {
    // If no ID was provided, just go back to the roster
    header("Location: roster_view.php");
    exit();
}

$conn->close();
?>