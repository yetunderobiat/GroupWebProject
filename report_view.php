<?php require_once 'report_logic.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style> body { font-family: 'Inter', sans-serif; background-color: #f8fafc; } </style>
</head>
<body class="text-gray-800">

    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-4">
                    <a href="seminar.html" class="text-gray-500 hover:text-gray-900 font-medium flex items-center gap-2">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i> Back
                    </a>
                    <div class="h-6 w-px bg-gray-300"></div>
                    <span class="text-sm font-bold text-gray-900">Reports & Analytics</span>
                </div>
                
                <div>
                    <a href="report_logic.php?export=csv<?php echo $selectedSeminarID ? '&seminar_id='.$selectedSeminarID : ''; ?>" class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center gap-2 px-3 py-2 border border-blue-100 rounded-lg hover:bg-blue-50 transition-colors">
                        <i data-lucide="download" class="w-4 h-4"></i> Export CSV
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <?php if ($selectedSeminarID > 0 && $seminarDetails): ?>
            
            <div class="bg-blue-600 rounded-xl p-6 text-white shadow-lg mb-8">
                <div class="flex justify-between items-start">
                    <h1 class="text-2xl font-bold mb-2"><?php echo htmlspecialchars($seminarDetails['Topic']); ?></h1>
                    <a href="report_view.php" class="text-xs bg-white/20 hover:bg-white/30 px-3 py-1 rounded-full text-white transition-colors">Back to Dashboard</a>
                </div>
                <div class="flex flex-wrap gap-6 text-blue-100 text-sm">
                    <span class="flex items-center gap-1"><i data-lucide="calendar" class="w-4 h-4"></i> <?php echo date("F d, Y", strtotime($seminarDetails['EventDate'])); ?></span>
                    <span class="flex items-center gap-1"><i data-lucide="user" class="w-4 h-4"></i> <?php echo htmlspecialchars($seminarDetails['SpeakerName'] ?? 'Guest'); ?></span>
                </div>
                <p class="mt-4 text-blue-50 text-sm leading-relaxed max-w-2xl"><?php echo htmlspecialchars($seminarDetails['Description']); ?></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm"><p class="text-xs font-semibold text-gray-500 uppercase">Present</p><p class="text-3xl font-bold text-green-600 mt-1"><?php echo $specificStats['present']; ?></p></div>
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm"><p class="text-xs font-semibold text-gray-500 uppercase">Absent</p><p class="text-3xl font-bold text-red-600 mt-1"><?php echo $specificStats['absent']; ?></p></div>
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm"><p class="text-xs font-semibold text-gray-500 uppercase">Rate</p><p class="text-3xl font-bold text-blue-600 mt-1"><?php echo $specificStats['rate']; ?>%</p></div>
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm"><p class="text-xs font-semibold text-gray-500 uppercase">Class Size</p><p class="text-3xl font-bold text-gray-700 mt-1"><?php echo $specificStats['total']; ?></p></div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">Absentee List (This Seminar)</h3>
                    <span class="text-xs font-medium text-red-600 bg-red-50 px-2 py-1 rounded-full"><?php echo $specificStats['absent']; ?> Missing</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matric No</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Faculty</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Level</th></tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if ($specificAbsentees && $specificAbsentees->num_rows > 0): 
                                while($stu = $specificAbsentees->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900"><?php echo htmlspecialchars($stu['FullName']); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-500 font-mono"><?php echo htmlspecialchars($stu['MatricNo']); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-500"><?php echo htmlspecialchars($stu['Faculty']); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-500"><?php echo htmlspecialchars($stu['Level']); ?> Lvl</td>
                                </tr>
                            <?php endwhile; else: ?>
                                <tr><td colspan="4" class="px-6 py-10 text-center text-gray-500">🎉 Everyone is present!</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php else: ?>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                    <div class="text-xs font-semibold text-gray-500 uppercase">Total Seminars</div>
                    <div class="text-3xl font-bold text-gray-900 mt-1"><?php echo $totalSeminars; ?></div>
                </div>
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                    <div class="text-xs font-semibold text-gray-500 uppercase">Average Attendance</div>
                    <div class="text-3xl font-bold text-green-600 mt-1"><?php echo $avgAttendanceRate; ?>%</div>
                </div>
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                    <div class="text-xs font-semibold text-gray-500 uppercase">Total Absentees</div>
                    <div class="text-3xl font-bold text-red-600 mt-1"><?php echo $totalAbsentees; ?></div>
                </div>
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                    <div class="text-xs font-semibold text-gray-500 uppercase">Best Faculty</div>
                    <div class="text-2xl font-bold text-blue-600 mt-1 truncate" title="<?php echo $bestFacultyName; ?>"><?php echo $bestFacultyName; ?></div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm mb-8 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800">Faculty Analysis</h3>
                </div>
                
                <div class="p-6">
                    <div class="w-full h-80 mb-8">
                        <canvas id="facultyChart"></canvas>
                    </div>

                    <div class="overflow-hidden rounded-lg border border-gray-200 w-full">
                        <table class="min-w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Faculty</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Present</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Students</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Attendance Rate</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php foreach($facultyStats as $stat): ?>
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900"><?php echo $stat['name']; ?></td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-600"><?php echo $stat['present_total']; ?></td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-600"><?php echo $stat['student_count']; ?></td>
                                    <td class="px-4 py-3 text-sm text-right font-bold <?php echo ($stat['rate'] < 50) ? 'text-red-600' : 'text-blue-600'; ?>"><?php echo $stat['rate']; ?>%</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <h3 class="text-lg font-bold text-gray-800">Recent Absentees</h3>
                    
                    <form method="GET" class="flex items-center gap-3">
                        <select name="faculty_filter" onchange="this.form.submit()" class="bg-white border border-gray-300 text-gray-700 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2">
                            <option value="All" <?php if($filterFaculty=='All') echo 'selected'; ?>>All Faculties</option>
                            <option value="Computing" <?php if($filterFaculty=='Computing') echo 'selected'; ?>>Computing</option>
                            <option value="Engineering" <?php if($filterFaculty=='Engineering') echo 'selected'; ?>>Engineering</option>
                            <option value="Medicine" <?php if($filterFaculty=='Medicine') echo 'selected'; ?>>Medicine</option>
                            <option value="Law" <?php if($filterFaculty=='Law') echo 'selected'; ?>>Law</option>
                            <option value="Business" <?php if($filterFaculty=='Business') echo 'selected'; ?>>Business</option>
                        </select>
                        
                        <button type="submit" name="export_absent" value="true" class="text-blue-600 hover:text-blue-800 text-xs font-medium flex items-center gap-1 border border-blue-200 px-3 py-2 rounded-lg hover:bg-blue-50 transition-colors">
                            <i data-lucide="download" class="w-3 h-3"></i> CSV
                        </button>
                    </form>
                </div>
                
                <div class="overflow-x-auto max-h-96">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matric No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Faculty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Seminar Missed</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if ($globalAbsentees && $globalAbsentees->num_rows > 0): 
                                while($row = $globalAbsentees->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 text-sm font-medium text-gray-900"><?php echo htmlspecialchars($row['FullName']); ?></td>
                                    <td class="px-6 py-3 text-sm text-gray-500 font-mono"><?php echo htmlspecialchars($row['MatricNo']); ?></td>
                                    <td class="px-6 py-3 text-sm text-gray-500"><?php echo htmlspecialchars($row['Faculty']); ?></td>
                                    <td class="px-6 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($row['Topic']); ?></td>
                                    <td class="px-6 py-3 text-sm text-gray-500"><?php echo date("M d", strtotime($row['EventDate'])); ?></td>
                                </tr>
                            <?php endwhile; else: ?>
                                <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No absences found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-800">Seminar History</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    <?php if (count($seminarHistory) > 0): foreach($seminarHistory as $hist): ?>
                        <div class="p-5 flex flex-col md:flex-row md:items-center justify-between hover:bg-gray-50 transition-all gap-4">
                            <div class="flex items-center gap-5">
                                <div class="relative w-14 h-14 flex items-center justify-center rounded-full border-4 border-blue-100 bg-white shrink-0">
                                    <span class="text-sm font-bold text-blue-600"><?php echo $hist['rate']; ?>%</span>
                                </div>
                                <div>
                                    <h4 class="text-base font-bold text-gray-900"><?php echo htmlspecialchars($hist['topic']); ?></h4>
                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1 text-xs text-gray-500 font-medium">
                                        <span class="flex items-center gap-1"><i data-lucide="calendar" class="w-3 h-3"></i> <?php echo $hist['date']; ?></span>
                                        <span class="flex items-center gap-1"><i data-lucide="user" class="w-3 h-3"></i> <?php echo htmlspecialchars($hist['speaker']); ?></span>
                                    </div>
                                </div>
                            </div>
                            <a href="report_view.php?seminar_id=<?php echo $hist['id']; ?>" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:text-blue-600 shadow-sm transition-colors whitespace-nowrap">
                                View Details <i data-lucide="chevron-right" class="w-4 h-4 ml-1"></i>
                            </a>
                        </div>
                    <?php endforeach; else: ?>
                        <div class="p-10 text-center text-gray-500">No seminar history found.</div>
                    <?php endif; ?>
                </div>
            </div>

            <script>
                const ctx = document.getElementById('facultyChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode(array_column($facultyStats, 'name')); ?>,
                        datasets: [{
                            label: 'Attendance Rate (%)',
                            data: <?php echo json_encode(array_column($facultyStats, 'rate')); ?>,
                            backgroundColor: '#1a73e8', // SOLID BLUE color
                            borderRadius: 4,
                            barThickness: 50, // Thick bars like your image
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, max: 100 }, x: { grid: { display: false } } }
                    }
                });
            </script>

        <?php endif; ?>

    </main>
    <script> lucide.createIcons(); </script>
</body>
</html>