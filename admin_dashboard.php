<?php
session_start();
require 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// --- DATA FETCHING ---

// 1. Fetch data for Analytics Chart
$stmtAnalytics = $pdo->query("SELECT category, COUNT(*) as count FROM complaints GROUP BY category");
$analyticsData = $stmtAnalytics->fetchAll();
$chartLabels = [];
$chartData = [];
foreach ($analyticsData as $data) {
    $chartLabels[] = $data['category'];
    $chartData[] = $data['count'];
}
$chartLabelsJson = json_encode($chartLabels);
$chartDataJson   = json_encode($chartData);

// 2. Fetch all complaints for the list
$stmtComplaints = $pdo->query("SELECT id, category, message, status, created_at FROM complaints ORDER BY created_at DESC");
$complaints = $stmtComplaints->fetchAll();

// 3. Fetch data for Stat Cards
$totalComplaints = $pdo->query("SELECT COUNT(*) FROM complaints")->fetchColumn();
$openComplaints = $pdo->query("SELECT COUNT(*) FROM complaints WHERE status = 'Open'")->fetchColumn();
$resolvedComplaints = $pdo->query("SELECT COUNT(*) FROM complaints WHERE status = 'Resolved'")->fetchColumn();


// --- HELPER FUNCTIONS ---

// Helper to get color class for status badges
function getStatusBadge($status) {
    switch (strtolower($status)) {
        case 'resolved':
            return 'bg-green-100 text-green-800';
        case 'in progress':
            return 'bg-yellow-100 text-yellow-800';
        case 'open':
        default:
            return 'bg-blue-100 text-blue-800';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">

<div class="flex h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-800 text-white flex flex-col">
        <div class="p-6 text-2xl font-bold border-b border-gray-700">
            CMS Admin
        </div>
        <nav class="flex-1 p-4 space-y-2">
            <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-lg bg-gray-900 text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a2 2 0 002 2h10a2 2 0 002-2V10M9 20h6"></path></svg>
                <span>Dashboard</span>
            </a>
            <a href="logout.php" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                <span>Logout</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6 sm:p-8 lg:p-10 overflow-y-auto">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
            <p class="text-gray-600 mt-1">Overview of the complaint management system.</p>
        </header>

        <!-- Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200/80">
                <h3 class="text-gray-500 font-medium">Total Complaints</h3>
                <p class="text-4xl font-bold text-gray-900 mt-2"><?php echo $totalComplaints; ?></p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200/80">
                <h3 class="text-gray-500 font-medium">Open Cases</h3>
                <p class="text-4xl font-bold text-blue-600 mt-2"><?php echo $openComplaints; ?></p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200/80">
                <h3 class="text-gray-500 font-medium">Resolved Cases</h3>
                <p class="text-4xl font-bold text-green-600 mt-2"><?php echo $resolvedComplaints; ?></p>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Complaints List (takes 2/3 width on large screens) -->
            <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-md border border-gray-200/80">
                <h2 class="text-xl font-bold text-gray-900 mb-4">All Complaints</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3">ID</th>
                                <th scope="col" class="px-6 py-3">Category</th>
                                <th scope="col" class="px-6 py-3">Message</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                                <th scope="col" class="px-6 py-3">Date</th>
                                <th scope="col" class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($complaints)): ?>
                                <tr><td colspan="6" class="text-center py-8">No complaints found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($complaints as $comp): ?>
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 font-mono text-xs"><?php echo htmlspecialchars($comp['id']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($comp['category']); ?></td>
                                    <td class="px-6 py-4 max-w-xs truncate" title="<?php echo htmlspecialchars($comp['message']); ?>"><?php echo htmlspecialchars($comp['message']); ?></td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs <?php echo getStatusBadge($comp['status']); ?>">
                                            <?php echo htmlspecialchars($comp['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4"><?php echo date("M d, Y", strtotime($comp['created_at'])); ?></td>
                                    <td class="px-6 py-4 space-x-2 whitespace-nowrap">
                                        <a href="update_status.php?id=<?php echo $comp['id']; ?>&status=In+Progress" class="font-medium text-yellow-600 hover:underline">In Progress</a>
                                        <a href="update_status.php?id=<?php echo $comp['id']; ?>&status=Resolved" class="font-medium text-green-600 hover:underline">Resolve</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Analytics Chart (takes 1/3 width on large screens) -->
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200/80">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Analytics by Category</h2>
                <div class="h-80">
                    <canvas id="analyticsChart"></canvas>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('analyticsChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut', // Changed to doughnut for a modern look
        data: {
            labels: <?php echo $chartLabelsJson; ?>,
            datasets: [{
                label: 'Complaint Count',
                data: <?php echo $chartDataJson; ?>,
                backgroundColor: [
                    'rgba(79, 70, 229, 0.8)',
                    'rgba(13, 148, 136, 0.8)',
                    'rgba(217, 119, 6, 0.8)',
                    'rgba(220, 38, 38, 0.8)',
                    'rgba(107, 114, 128, 0.8)'
                ],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    backgroundColor: '#111827',
                    padding: 10,
                    cornerRadius: 5
                }
            }
        }
    });
});
</script>
</body>
</html>
