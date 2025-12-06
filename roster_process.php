<?php
// roster_process.php - Student Registration (No Email, No Seating)

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ChapelSeminarDB";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Get Data (No Email)
    $fullName = trim($_POST['fullName']);
    $matricNo = trim($_POST['matricNo']);
    $faculty = trim($_POST['faculty']);
    $level = intval($_POST['level']);

    if (!empty($fullName) && !empty($matricNo)) {
        // 2. Insert into Database (Columns: MatricNo, FullName, Faculty, Level)
        $stmt = $conn->prepare("INSERT INTO Student (MatricNo, FullName, Faculty, Level) VALUES (?, ?, ?, ?)");
        
        // Bind params: s=string, s=string, s=string, i=integer
        $stmt->bind_param("sssi", $matricNo, $fullName, $faculty, $level);

        if ($stmt->execute()) {
            echo "<script>alert('Student Added Successfully!'); window.location.href='roster_view.php';</script>";
        } else {
            // Duplicate Check
            if ($conn->errno == 1062) {
                echo "<script>alert('Error: Matric Number already exists.'); window.history.back();</script>";
            } else {
                echo "<script>alert('Database Error: " . $conn->error . "'); window.history.back();</script>";
            }
        }
        $stmt->close();
    }
}
$conn->close();
?>