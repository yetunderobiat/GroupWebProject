<?php require_once 'attendance_logic.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Tracking - Babcock University</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .toggle-checkbox:checked {
            right: 0;
            border-color: #2563eb;
        }
        .toggle-checkbox:checked + .toggle-label {
            background-color: #2563eb;
        }
    </style>
    <script>
        // Client-side Logic for "Mark All" and Stats Update
        function toggleAll(master) {
            let checkboxes = document.querySelectorAll('.student-checkbox');
            checkboxes.forEach(cb => cb.checked = master.checked);
            updateStats();
        }

        function updateStats() {
            let total = document.querySelectorAll('.student-checkbox').length;
            let checked = document.querySelectorAll('.student-checkbox:checked').length;
            let absent = total - checked;
            let rate = total > 0 ? Math.round((checked / total) * 100) : 0;

            document.getElementById('stat-present').innerText = checked;
            document.getElementById('stat-absent').innerText = absent;
            document.getElementById('stat-rate').innerText = rate + '%';
        }
    </script>
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
                    <span class="text-sm font-bold text-gray-900">Attendance Tracker</span>
                </div>
                
                <div class="flex items-center">
                    <form method="GET" class="flex items-center gap-2">
                        <select name="seminar_id" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-64 p-2.5">
                            <option value="">-- Select Event --</option>
                            <?php if($seminars) while($r=$seminars->fetch_assoc()): ?>
                                <option value="<?php echo $r['SeminarID']; ?>" <?php echo ($r['SeminarID']==$selectedSeminarID)?'selected':''; ?>>
                                    <?php echo $r['Topic']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <?php if ($selectedSeminarID): ?>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Total Students</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $totalStudents; ?></p>
                    </div>
                    <div class="p-2 bg-blue-50 rounded-lg text-blue-600"><i data-lucide="users" class="w-5 h-5"></i></div>
                </div>
                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Present</p>
                        <p class="text-2xl font-bold text-green-600" id="stat-present">0</p>
                    </div>
                    <div class="p-2 bg-green-50 rounded-lg text-green-600"><i data-lucide="user-check" class="w-5 h-5"></i></div>
                </div>
                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Absent</p>
                        <p class="text-2xl font-bold text-red-600" id="stat-absent"><?php echo $totalStudents; ?></p>
                    </div>
                    <div class="p-2 bg-red-50 rounded-lg text-red-600"><i data-lucide="user-x" class="w-5 h-5"></i></div>
                </div>
                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Attendance Rate</p>
                        <p class="text-2xl font-bold text-gray-900" id="stat-rate">0%</p>
                    </div>
                    <div class="p-2 bg-gray-50 rounded-lg text-gray-600"><i data-lucide="activity" class="w-5 h-5"></i></div>
                </div>
            </div>

            <div class="flex justify-between items-center mb-6 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="text-sm font-semibold text-gray-700">Quick Actions:</span>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer" onchange="toggleAll(this)">
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        <span class="ms-3 text-sm font-medium text-gray-900">Mark All Present</span>
                    </label>
                </div>
                <button type="submit" form="attForm" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i> Save Attendance
                </button>
            </div>

            <form id="attForm" method="POST" action="attendance_process.php">
                <input type="hidden" name="seminar_id" value="<?php echo $selectedSeminarID; ?>">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <?php if ($students && $students->num_rows > 0): 
                        while($stu = $students->fetch_assoc()): 
                            // Determine if student was previously present
                            // Note: You might need to update logic.php to fetch current status if editing
                    ?>
                        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-sm">
                                    <?php echo strtoupper(substr($stu['FullName'], 0, 2)); ?>
                                </div>
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($stu['FullName']); ?></h4>
                                    <div class="text-xs text-gray-500 flex items-center gap-2">
                                        <span><?php echo htmlspecialchars($stu['MatricNo']); ?></span>
                                        <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                        <span><?php echo htmlspecialchars($stu['Level']); ?> Lvl</span>
                                    </div>
                                </div>
                            </div>
                            
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="attendance[]" value="<?php echo $stu['StudentID']; ?>" class="sr-only peer student-checkbox" onchange="updateStats()">
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                            </label>
                        </div>
                    <?php endwhile; endif; ?>
                </div>
            </form>

        <?php else: ?>
            <div class="text-center py-20 bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="bg-blue-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-blue-600">
                    <i data-lucide="calendar" class="w-8 h-8"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Select a Seminar</h3>
                <p class="text-gray-500 mt-1">Choose an event from the top right dropdown to start tracking attendance.</p>
            </div>
        <?php endif; ?>

    </main>

    <script>
        lucide.createIcons();
        // Initial Stat Calculation (in case page reloads with checked boxes)
        updateStats();
    </script>
</body>
</html>