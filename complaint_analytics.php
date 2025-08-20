<?php
// This block remains the same.
// It assumes $pdo is your database connection object.
// require 'db.php'; // Make sure to require your DB connection file.

// --- MOCK DATA FOR STANDALONE DEMO ---
// In your real app, you would use the database query.
// This is just so the chart has data to display if you run this file alone.
if (!isset($pdo)) {
    $analyticsData = [
        ['category' => 'HR', 'count' => 8],
        ['category' => 'IT Support', 'count' => 12],
        ['category' => 'Workload', 'count' => 5],
        ['category' => 'Safety', 'count' => 3],
    ];
} else {
    // --- YOUR ORIGINAL DATABASE QUERY ---
    $stmt = $pdo->query("SELECT category, COUNT(*) as count FROM complaints GROUP BY category");
    $analyticsData = $stmt->fetchAll();
}
// --- END OF DATA FETCHING ---


$chartLabels = [];
$chartData = [];
foreach ($analyticsData as $data) {
    $chartLabels[] = $data['category'];
    $chartData[] = $data['count'];
}

$chartLabelsJson = json_encode($chartLabels);
$chartDataJson   = json_encode($chartData);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Analytics</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='28' height='49' viewBox='0 0 28 49'%3E%3Cg fill-rule='evenodd'%3E%3Cg id='hexagons' fill='%23e2e8f0' fill-opacity='0.4' fill-rule='nonzero'%3E%3Cpath d='M13.99 9.25l13 7.5v15l-13 7.5L1 31.75v-15l12.99-7.5zM3 17.9v12.7l10.99 6.34 11-6.35V17.9l-11-6.34L3 17.9zM0 15l12.99-7.5L26 15v18.5l-13 7.5L0 33.5V15z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <!-- Complaint Analytics Card -->
    <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-200/50 w-full max-w-2xl">
        <h2 class="text-xl font-bold mb-1 text-gray-900">Complaint Analytics</h2>
        <p class="text-gray-500 mb-6">Distribution of complaints by category.</p>
        <div class="w-full h-80 flex items-center justify-center">
            <canvas id="analyticsChart"></canvas>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('analyticsChart').getContext('2d');
    const chartLabels = <?php echo $chartLabelsJson; ?>;
    const chartData = <?php echo $chartDataJson; ?>;

    const analyticsChart = new Chart(ctx, {
        type: 'doughnut', // Changed to doughnut for a modern look
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Complaint Count',
                data: chartData,
                backgroundColor: [ // Professional color palette
                    'rgba(79, 70, 229, 0.8)',  // Indigo
                    'rgba(13, 148, 136, 0.8)', // Teal
                    'rgba(217, 119, 6, 0.8)',  // Amber
                    'rgba(220, 38, 38, 0.8)',  // Red
                    'rgba(107, 114, 128, 0.8)',// Gray
                    'rgba(30, 64, 175, 0.8)'  // Blue
                ],
                borderColor: '#fff',
                borderWidth: 2,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%', // Makes the doughnut hole larger
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        font: {
                            family: "'Inter', sans-serif",
                            size: 12
                        },
                        boxWidth: 15,
                        padding: 15
                    }
                },
                tooltip: {
                    backgroundColor: '#111827',
                    titleFont: { size: 14, family: "'Inter', sans-serif" },
                    bodyFont: { size: 12, family: "'Inter', sans-serif" },
                    padding: 12,
                    cornerRadius: 6,
                    displayColors: false, // Hides the color box in tooltip
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed !== null) {
                                label += context.parsed;
                            }
                            return ' ' + label;
                        }
                    }
                }
            }
        }
    });
});
</script>
</body>
</html>
