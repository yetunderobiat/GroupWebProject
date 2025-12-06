<?php
// attendance_process.php - Handles the form submission to save attendance

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

// 2. Check if Form was Submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate Seminar ID
    $seminarID = isset($_POST['seminar_id']) ? $_POST['seminar_id'] : '';
    
    if (empty($seminarID)) {
        echo "<script>alert('Error: No seminar selected.'); window.location.href='attendance_view.php';</script>";
        exit;
    }

    // Get the array of checked Student IDs (if any)
    // If no boxes were checked, this variable might not be set, so we default to an empty array []
    $presentStudents = isset($_POST['attendance']) ? $_POST['attendance'] : [];

    $count = 0;

    // 3. Prepare the Statement (Security Best Practice)
    // We use ON DUPLICATE KEY UPDATE to handle cases where attendance was already taken.
    // This updates the check-in time if you scan them again.
    $stmt = $conn->prepare("INSERT INTO Attendance (SeminarID, StudentID, AttendanceStatus, CheckInTime) 
                            VALUES (?, ?, 'Present', NOW()) 
                            ON DUPLICATE KEY UPDATE AttendanceStatus='Present', CheckInTime=NOW()");

    // 4. Loop through each checked student and save to DB
    foreach ($presentStudents as $studentID) {
        $stmt->bind_param("ii", $seminarID, $studentID);
        if ($stmt->execute()) {
            $count++;
        }
    }

    $stmt->close();
    $conn->close();

    // 5. Success Message & Redirect
    // We send the user back to the attendance page and keep the seminar selected
    echo "<script>
            alert('Success! Marked $count students as Present.'); 
            window.location.href='attendance_view.php?seminar_id=$seminarID';
          </script>";
} else {
    // If someone tries to open this file directly without submitting the form
    header("Location: attendance_view.php");
    exit;
}
?>