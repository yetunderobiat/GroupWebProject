<?php
// report_logic.php - Final Version with Filters & Analysis

error_reporting(E_ALL);
ini_set('display_errors', 0); 

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ChapelSeminarDB";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// --- INITIALIZE VARIABLES ---
$selectedSeminarID = isset($_GET['seminar_id']) ? intval($_GET['seminar_id']) : 0;
$filterFaculty = isset($_GET['faculty_filter']) ? $_GET['faculty_filter'] : 'All'; // NEW: Filter

$seminarDetails = null;
$specificAbsentees = null;
$specificStats = ['present'=>0, 'absent'=>0, 'rate'=>0, 'total'=>0];
$seminarHistory = [];
$facultyStats = []; 
$globalAbsentees = [];
$totalSeminars = 0;
$totalStudents = 0;
$avgAttendanceRate = 0;
$totalAbsentees = 0;
$bestFacultyName = "N/A";

// --- GLOBAL COUNTS ---
$totalStudents = $conn->query("SELECT COUNT(*) as c FROM Student")->fetch_assoc()['c'];
$totalSeminarsRes = $conn->query("SELECT COUNT(*) as c FROM Seminar");
if($totalSeminarsRes) $totalSeminars = $totalSeminarsRes->fetch_assoc()['c'];

// --- FETCH ALL SEMINARS (For Dropdown) ---
$allSeminars = $conn->query("SELECT SeminarID, Topic, EventDate FROM Seminar ORDER BY EventDate DESC");


// =========================================================
// LOGIC: SPECIFIC vs GLOBAL
// =========================================================

if ($selectedSeminarID > 0) {
    // === A. SPECIFIC SEMINAR MODE ===
    
    // 1. Details
    $stmt = $conn->prepare("SELECT Sem.*, Sp.SpeakerName FROM Seminar Sem 
                            LEFT JOIN Speaker Sp ON Sem.SpeakerID = Sp.SpeakerID 
                            WHERE Sem.SeminarID = ?");
    $stmt->bind_param("i", $selectedSeminarID);
    $stmt->execute();
    $seminarDetails = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // 2. Stats
    $res = $conn->query("SELECT COUNT(*) as c FROM Attendance WHERE SeminarID = $selectedSeminarID AND AttendanceStatus='Present'");
    $presentCount = $res ? $res->fetch_assoc()['c'] : 0;
    $absentCount = ($totalStudents > 0) ? ($totalStudents - $presentCount) : 0;
    $attendanceRate = ($totalStudents > 0) ? round(($presentCount / $totalStudents) * 100, 1) : 0;

    $specificStats = ['present' => $presentCount, 'absent' => $absentCount, 'rate' => $attendanceRate, 'total' => $totalStudents];

    // 3. Absentees
    $absentSql = "SELECT FullName, MatricNo, Faculty, Level FROM Student 
                  WHERE StudentID NOT IN (SELECT StudentID FROM Attendance WHERE SeminarID = $selectedSeminarID)
                  ORDER BY Faculty ASC, FullName ASC";
    $specificAbsentees = $conn->query($absentSql);

} else {
    // === B. GLOBAL DASHBOARD MODE ===
    
    // 1. Global Stats
    $res = $conn->query("SELECT COUNT(*) as c FROM Attendance WHERE AttendanceStatus='Present'");
    $totalPresent = $res ? $res->fetch_assoc()['c'] : 0;
    $totalPossible = $totalStudents * $totalSeminars;
    $avgAttendanceRate = ($totalPossible > 0) ? round(($totalPresent / $totalPossible) * 100, 1) : 0;
    $totalAbsentees = ($totalPossible > 0) ? ($totalPossible - $totalPresent) : 0;

    // 2. Best Faculty
    $sqlBest = "SELECT St.Faculty, COUNT(A.AttendanceID) as PresentCount 
                FROM Student St JOIN Attendance A ON St.StudentID = A.StudentID 
                GROUP BY St.Faculty ORDER BY PresentCount DESC LIMIT 1";
    $resBest = $conn->query($sqlBest);
    if($resBest && $row = $resBest->fetch_assoc()) $bestFacultyName = $row['Faculty'];

    // 3. FACULTY PERFORMANCE (Chart & Table)
    $faculties = ["Computing", "Engineering", "Medicine", "Law", "Business", "Sciences"];
    
    foreach($faculties as $fac) {
        $sCount = $conn->query("SELECT COUNT(*) as c FROM Student WHERE Faculty = '$fac'")->fetch_assoc()['c'];
        $attCount = $conn->query("SELECT COUNT(*) as c FROM Attendance A JOIN Student S ON A.StudentID = S.StudentID WHERE S.Faculty = '$fac'")->fetch_assoc()['c'];
        $poss = $sCount * $totalSeminars;
        $rate = ($poss > 0) ? round(($attCount / $poss) * 100, 1) : 0;
        
        $colorClass = 'bg-gray-100 text-gray-800'; $hex = '#6b7280';
        if($fac == 'Computing') { $colorClass='bg-blue-100 text-blue-800'; $hex='#1d4ed8'; }
        if($fac == 'Engineering') { $colorClass='bg-green-100 text-green-800'; $hex='#15803d'; }
        if($fac == 'Medicine') { $colorClass='bg-red-100 text-red-800'; $hex='#b91c1c'; }
        if($fac == 'Law') { $colorClass='bg-purple-100 text-purple-800'; $hex='#7e22ce'; }
        if($fac == 'Business') { $colorClass='bg-orange-100 text-orange-800'; $hex='#c2410c'; }

        if ($sCount > 0 || $attCount > 0) {
            $facultyStats[] = ['name' => $fac, 'rate' => $rate, 'present_total' => $attCount, 'student_count' => $sCount, 'class' => $colorClass, 'hex' => $hex];
        }
    }

    // 4. GLOBAL ABSENTEE REPORT (Filtered)
    // "Which students missed which seminar?"
    $absentQuery = "SELECT St.FullName, St.MatricNo, St.Faculty, Sem.Topic, Sem.EventDate 
                    FROM Student St 
                    CROSS JOIN Seminar Sem 
                    LEFT JOIN Attendance A ON St.StudentID = A.StudentID AND Sem.SeminarID = A.SeminarID 
                    WHERE A.AttendanceID IS NULL";
    
    // Apply Filter
    if ($filterFaculty != 'All') {
        $absentQuery .= " AND St.Faculty = '$filterFaculty'";
    }
    
    $absentQuery .= " ORDER BY Sem.EventDate DESC LIMIT 100"; // Limit for display
    $globalAbsentees = $conn->query($absentQuery);

    // 5. Seminar History List
    $histSql = "SELECT Sem.*, Sp.SpeakerName FROM Seminar Sem 
                LEFT JOIN Speaker Sp ON Sem.SpeakerID = Sp.SpeakerID 
                ORDER BY Sem.EventDate DESC LIMIT 10";
    $histRes = $conn->query($histSql);

    if ($histRes) {
        while ($row = $histRes->fetch_assoc()) {
            $sID = $row['SeminarID'];
            $pCountRes = $conn->query("SELECT COUNT(*) as c FROM Attendance WHERE SeminarID=$sID");
            $pCount = $pCountRes ? $pCountRes->fetch_assoc()['c'] : 0;
            $rate = ($totalStudents > 0) ? round(($pCount / $totalStudents) * 100) : 0;

            $seminarHistory[] = [
                'id' => $sID, 'topic' => $row['Topic'], 'date' => date("M d, Y", strtotime($row['EventDate'])),
                'faculty' => $row['Faculty'], 'speaker' => $row['SpeakerName'] ?? 'Guest', 'rate' => $rate
            ];
        }
    }
}

// --- EXPORT HANDLERS ---

// 1. Export ABSENTEE REPORT (Respects Filter)
if (isset($_GET['export_absent'])) {
    ob_end_clean();
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="absentee_report.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, array('Name', 'Matric', 'Faculty', 'Seminar Missed', 'Date'));
    
    // Re-run query without limit for export
    $expSql = "SELECT St.FullName, St.MatricNo, St.Faculty, Sem.Topic, Sem.EventDate 
               FROM Student St CROSS JOIN Seminar Sem 
               LEFT JOIN Attendance A ON St.StudentID = A.StudentID AND Sem.SeminarID = A.SeminarID 
               WHERE A.AttendanceID IS NULL";
    if ($filterFaculty != 'All') $expSql .= " AND St.Faculty = '$filterFaculty'";
    $expSql .= " ORDER BY Sem.EventDate DESC";
    
    $res = $conn->query($expSql);
    while ($row = $res->fetch_assoc()) {
        fputcsv($output, array($row['FullName'], $row['MatricNo'], $row['Faculty'], $row['Topic'], $row['EventDate']));
    }
    fclose($output);
    exit();
}

// 2. Export MAIN (Specific or History)
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    ob_end_clean();
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="report.csv"');
    $output = fopen('php://output', 'w');

    if ($selectedSeminarID > 0) {
        fputcsv($output, array('Name', 'Matric', 'Faculty', 'Level', 'Status'));
        if ($specificAbsentees) {
            $specificAbsentees->data_seek(0);
            while ($row = $specificAbsentees->fetch_assoc()) {
                fputcsv($output, array($row['FullName'], $row['MatricNo'], $row['Faculty'], $row['Level'], 'Absent'));
            }
        }
    } else {
        fputcsv($output, array('Topic', 'Date', 'Speaker', 'Attendance Rate'));
        foreach($seminarHistory as $h) {
            fputcsv($output, array($h['topic'], $h['date'], $h['speaker'], $h['rate'].'%'));
        }
    }
    fclose($output);
    exit();
}
?>