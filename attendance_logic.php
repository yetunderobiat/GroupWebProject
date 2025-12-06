<?php
// attendance_logic.php - Logic for Attendance View

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ChapelSeminarDB";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// 1. Fetch Seminars
$seminars = $conn->query("SELECT SeminarID, Topic, EventDate FROM Seminar ORDER BY EventDate DESC");

// 2. Fetch Data if Seminar Selected
$selectedSeminarID = isset($_GET['seminar_id']) ? $_GET['seminar_id'] : '';
$students = null;
$totalStudents = 0;

if ($selectedSeminarID) {
    // Get total count first for stats
    $countRes = $conn->query("SELECT COUNT(*) as c FROM Student");
    $totalStudents = $countRes->fetch_assoc()['c'];

    // Get all students sorted alphabetically
    $students = $conn->query("SELECT * FROM Student ORDER BY FullName ASC");
}
?>