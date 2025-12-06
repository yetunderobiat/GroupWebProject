<?php
// create_seminar.php - Handles Single Select Levels

$servername = "localhost";
$username = "root";       
$password = "";           
$dbname = "ChapelSeminarDB"; 

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: seminar.html");
    exit;
}

// 1. Collect Form Data
$topic = trim($_POST['topic'] ?? '');
$description = trim($_POST['description'] ?? '');
$eventDate = trim($_POST['eventDate'] ?? '');
$eventTime = trim($_POST['eventTime'] ?? '');
$faculty = trim($_POST['faculty'] ?? '');
$venueID = trim($_POST['venueID'] ?? '');
$speakerName = trim($_POST['speakerName'] ?? '');
$speakerBio = trim($_POST['speakerBio'] ?? '');

// --- HANDLE SINGLE LEVEL ---
// It is now a simple string, defaulting to 'All'
$targetLevels = trim($_POST['target_levels'] ?? 'All');

// Basic Validation
if (empty($topic) || empty($eventDate) || empty($eventTime) || empty($speakerName) || empty($venueID)) {
    die("Error: Please fill in all required fields.");
}

// 2. Connect
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$conn->begin_transaction();

try {
    // A. Insert Speaker
    $stmt_speaker = $conn->prepare("INSERT INTO Speaker (SpeakerName, SpeakerBio) VALUES (?, ?)");
    $stmt_speaker->bind_param("ss", $speakerName, $speakerBio);
    if (!$stmt_speaker->execute()) throw new Exception("Speaker Error: " . $conn->error);
    $speakerID = $conn->insert_id;
    $stmt_speaker->close();

    // B. Insert Seminar
    $stmt_seminar = $conn->prepare("INSERT INTO Seminar (Topic, Description, TargetLevels, EventDate, EventTime, Faculty, VenueID, SpeakerID) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt_seminar->bind_param("ssssssii", $topic, $description, $targetLevels, $eventDate, $eventTime, $faculty, $venueID, $speakerID);
    
    if (!$stmt_seminar->execute()) {
        if ($conn->errno == 1062) throw new Exception("Venue already booked for this time.");
        throw new Exception("Seminar Error: " . $conn->error);
    }
    $stmt_seminar->close();

    $conn->commit();
    echo "<script>alert('Seminar Created! Level: $targetLevels'); window.location.href='seminar.html';</script>";

} catch (Exception $e) {
    $conn->rollback();
    echo "Failed: " . $e->getMessage();
}

$conn->close();
?>