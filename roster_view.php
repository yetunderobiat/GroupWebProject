<?php
// roster_view.php - Student Roster with Import/Export & New Layout

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ChapelSeminarDB";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// --- 1. HANDLE EXPORT CSV ---
if (isset($_GET['action']) && $_GET['action'] == 'export') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="student_roster.csv"');
    $output = fopen('php://output', 'w');
    
    // CSV Column Headers
    fputcsv($output, array('FullName', 'MatricNo', 'Faculty', 'Level', 'Email'));
    
    $query = "SELECT FullName, MatricNo, Faculty, Level, Email FROM Student ORDER BY FullName ASC";
    $result = $conn->query($query);
    while($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit(); // Stop script so HTML doesn't get appended
}

// --- 2. HANDLE IMPORT CSV ---
if (isset($_POST['import_btn'])) {
    if (is_uploaded_file($_FILES['csv_file']['tmp_name'])) {
        $csvFile = fopen($_FILES['csv_file']['tmp_name'], 'r');
        
        // Skip the first line (Header row)
        fgetcsv($csvFile); 
        
        $imported = 0;
        $stmt = $conn->prepare("INSERT IGNORE INTO Student (FullName, MatricNo, Faculty, Level, Email) VALUES (?, ?, ?, ?, ?)");
        
        while (($line = fgetcsv($csvFile)) !== FALSE) {
            // Check if row is not empty
            if(count($line) >= 4) {
                $name = $line[0];
                $matric = $line[1];
                $faculty = $line[2];
                $level = intval($line[3]);
                $email = isset($line[4]) ? $line[4] : ''; // Optional Email
                
                $stmt->bind_param("sssis", $name, $matric, $faculty, $level, $email);
                if($stmt->execute() && $stmt->affected_rows > 0) {
                    $imported++;
                }
            }
        }
        fclose($csvFile);
        $stmt->close();
        
        echo "<script>alert('Success! Imported $imported students.'); window.location.href='roster_view.php';</script>";
    }
}

// --- 3. FETCH DATA ---
// Handle Filter
$filterLevel = isset($_GET['level']) ? $_GET['level'] : 'All';
$sql = "SELECT * FROM Student";
if ($filterLevel != 'All') {
    $sql .= " WHERE Level = '$filterLevel'";
}
$sql .= " ORDER BY FullName ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Roster - Babcock University</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #ffffff; }
        .dropdown-menu { display: none; position: absolute; z-index: 50; background-color: white; border: 1px solid #e5e7eb; border-radius: 0.5rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); width: 12rem; right: 0; top: 100%; margin-top: 0.5rem; }
        .dropdown-menu.show { display: block; }
    </style>
</head>
<body class="text-gray-800">

    <nav class="border-b border-gray-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-4">
                    <a href="seminar.html" class="text-gray-500 hover:text-gray-900 font-medium flex items-center gap-2">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i> Back to Dashboard
                    </a>
                    <div class="h-6 w-px bg-gray-300"></div>
                    <span class="text-sm font-bold text-gray-900">Seminar System</span>
                </div>
                <div class="flex items-center gap-4">
                    <button class="p-2 text-gray-400 hover:text-gray-600"><i data-lucide="bell" class="w-5 h-5"></i></button>
                    <button class="p-2 text-gray-400 hover:text-gray-600"><i data-lucide="user" class="w-5 h-5"></i></button>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Student Roster</h1>
            <p class="text-sm text-gray-500 mt-1 mb-4">View and manage registered students.</p>
            
            <div class="flex flex-wrap items-center gap-3">
                
                <a href="roster_add.html" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium shadow-sm transition-colors">
                    <i data-lucide="plus" class="w-4 h-4"></i> Add Student
                </a>

                <form action="roster_view.php" method="POST" enctype="multipart/form-data" id="importForm" class="inline-flex">
                    <input type="file" name="csv_file" id="csvInput" accept=".csv" class="hidden" onchange="document.getElementById('importSubmitBtn').click()">
                    <button type="button" onclick="document.getElementById('csvInput').click()" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition-colors">
                        <i data-lucide="upload" class="w-4 h-4"></i> Import CSV
                    </button>
                    <button type="submit" name="import_btn" id="importSubmitBtn" class="hidden"></button>
                </form>

                <a href="roster_view.php?action=export" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition-colors">
                    <i data-lucide="download" class="w-4 h-4"></i> Export
                </a>

            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4 bg-gray-50/50">
                
                <div class="relative w-full sm:w-96">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search students..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm outline-none">
                </div>

                <div class="relative">
                    <button id="filter-btn" class="inline-flex items-center gap-2 px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <i data-lucide="filter" class="w-4 h-4"></i>
                        <span id="filter-text"><?php echo ($filterLevel == 'All') ? 'All Levels' : $filterLevel . ' Level'; ?></span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                    </button>
                    <div id="filter-menu" class="dropdown-menu">
                        <a href="roster_view.php?level=All" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">All Levels</a>
                        <a href="roster_view.php?level=100" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">100 Level</a>
                        <a href="roster_view.php?level=200" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">200 Level</a>
                        <a href="roster_view.php?level=300" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">300 Level</a>
                        <a href="roster_view.php?level=400" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">400 Level</a>
                        <a href="roster_view.php?level=500" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">500 Level</a>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matric Number</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Faculty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="studentTableBody">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm">
                                                <?php echo strtoupper(substr($row['FullName'], 0, 2)); ?>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($row['FullName']); ?></div>
                                            <div class="text-xs text-gray-500"><?php echo htmlspecialchars($row['Email']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                                    <?php echo htmlspecialchars($row['MatricNo']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <?php echo htmlspecialchars($row['Faculty']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo htmlspecialchars($row['Level']); ?> Lvl
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="delete_student.php?id=<?php echo $row['StudentID']; ?>" 
                                       class="text-gray-400 hover:text-red-600 transition-colors inline-block ml-2"
                                       onclick="return confirm('Delete this student?');">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500">No students found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        lucide.createIcons();
        const filterBtn = document.getElementById('filter-btn');
        const filterMenu = document.getElementById('filter-menu');
        filterBtn.addEventListener('click', (e) => { e.stopPropagation(); filterMenu.classList.toggle('show'); });
        window.addEventListener('click', () => { filterMenu.classList.remove('show'); });

        function searchTable() {
            let filter = document.getElementById("searchInput").value.toUpperCase();
            let rows = document.getElementById("studentTableBody").getElementsByTagName("tr");
            for (let i = 0; i < rows.length; i++) {
                let name = rows[i].getElementsByTagName("td")[0].textContent || "";
                let matric = rows[i].getElementsByTagName("td")[1].textContent || "";
                if (name.toUpperCase().indexOf(filter) > -1 || matric.toUpperCase().indexOf(filter) > -1) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>