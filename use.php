<?php
session_start();
require 'db.php'; // Include your database connection

// Initialize output message and chat history
$messageOutput = "";
$messageType = "success"; // Can be 'success' or 'error'
$chatHistory = isset($_SESSION['chatHistory']) ? $_SESSION['chatHistory'] : [];

// Process form submissions using a hidden "action" field
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action === "submit_complaint") {
    $cat = trim($_POST['category']);
    $msg = trim($_POST['message']);
    if (empty($cat) || empty($msg)) {
        $messageOutput = "Please fill in all fields to submit a complaint.";
        $messageType = "error";
    } else {
        $complaintId = uniqid("cmp_");
        $stmt = $pdo->prepare("INSERT INTO complaints (id, category, message, status) VALUES (?, ?, ?, 'Open')");
        $stmt->execute([$complaintId, $cat, $msg]);
        $messageOutput = "Complaint submitted successfully! Your Complaint ID is: " . $complaintId;
        $messageType = "success";
    }
}

if ($action === "track_complaint") {
    $trackId = trim($_POST['track_id']);
    if (empty($trackId)) {
        $messageOutput = "Please enter a Complaint ID to track.";
        $messageType = "error";
    } else {
        $stmt = $pdo->prepare("SELECT status FROM complaints WHERE id = ?");
        $stmt->execute([$trackId]);
        $complaint = $stmt->fetch();
        if ($complaint) {
            $messageOutput = "Status for complaint " . htmlspecialchars($trackId) . " is: " . htmlspecialchars($complaint['status']);
            $messageType = "success";
        } else {
            $messageOutput = "No complaint found with ID: " . htmlspecialchars($trackId);
            $messageType = "error";
        }
    }
}

if ($action === "send_email") {
    $messageOutput = "Simulated email notification sent to the admin!";
    $messageType = "success";
}

if ($action === "send_chat") {
    $chatMsg = trim($_POST['chat_message']);
    if (!empty($chatMsg)) {
        $_SESSION['chatHistory'][] = ["sender" => "User", "message" => $chatMsg];
        // Simulate an admin auto-reply for better UX
        $_SESSION['chatHistory'][] = ["sender" => "Admin", "message" => "Thanks for your message. We will get back to you shortly."];
        $chatHistory = $_SESSION['chatHistory'];
    }
}

if ($action === "send_otp") {
    $_SESSION['otp'] = "123456"; // Demo OTP
    $messageOutput = "A verification OTP has been sent.";
    $messageType = "success";
}

if ($action === "verify_otp") {
    $userOtp = trim($_POST['otp']);
    if (isset($_SESSION['otp']) && $userOtp === $_SESSION['otp']) {
        $messageOutput = "2FA Verified Successfully!";
        $messageType = "success";
        unset($_SESSION['otp']);
    } else {
        $messageOutput = "Invalid OTP. Please try again.";
        $messageType = "error";
    }
}

// Fetch analytics data
$stmt = $pdo->query("SELECT category, COUNT(*) as count FROM complaints GROUP BY category");
$analyticsData = $stmt->fetchAll();
$chartLabels = [];
$chartData = [];
foreach ($analyticsData as $data) {
    $chartLabels[] = $data['category'];
    $chartData[] = $data['count'];
}
$chartLabelsJson = json_encode($chartLabels);
$chartDataJson = json_encode($chartData);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anonymous Complaint & Feedback System</title>
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
        .chat-box::-webkit-scrollbar { width: 6px; }
        .chat-box::-webkit-scrollbar-track { background: #f1f1f1; }
        .chat-box::-webkit-scrollbar-thumb { background: #888; border-radius: 3px;}
        .chat-box::-webkit-scrollbar-thumb:hover { background: #555; }
    </style>
</head>
<body class="text-gray-800">

<div class="container mx-auto p-4 sm:p-6 lg:p-8">
    <header class="text-center mb-10">
        <h1 class="text-4xl font-bold text-gray-900">Complaint & Feedback Dashboard</h1>
        <p class="text-gray-600 mt-2">A secure and anonymous system for raising concerns.</p>
    </header>

    <?php if (!empty($messageOutput)): ?>
    <div class="mb-8 p-4 rounded-lg text-center <?php echo $messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
        <?php echo htmlspecialchars($messageOutput); ?>
    </div>
    <?php endif; ?>

    <main class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <!-- Left Column -->
        <div class="space-y-8">
            <!-- Submit Complaint Card -->
            <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-200/50">
                <h2 class="text-xl font-bold mb-4 text-gray-900">File a New Complaint</h2>
                <form method="post" action="" class="space-y-4">
                    <input type="hidden" name="action" value="submit_complaint">
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <input type="text" id="category" name="category" placeholder="e.g., HR, IT, Safety" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                        <textarea id="message" name="message" rows="4" placeholder="Describe your issue in detail..." required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 text-white py-2.5 px-4 rounded-lg font-semibold hover:bg-indigo-700 transition">Submit Securely</button>
                </form>
            </div>

            <!-- Track Complaint Card -->
            <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-200/50">
                <h2 class="text-xl font-bold mb-4 text-gray-900">Track Complaint Status</h2>
                <form method="post" action="" class="sm:flex sm:items-center sm:gap-4">
                    <input type="hidden" name="action" value="track_complaint">
                    <div class="w-full mb-4 sm:mb-0">
                        <label for="track_id" class="sr-only">Enter Complaint ID</label>
                        <input type="text" id="track_id" name="track_id" placeholder="Enter your complaint ID" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                    <button type="submit" class="w-full sm:w-auto flex-shrink-0 bg-teal-600 text-white py-2.5 px-6 rounded-lg font-semibold hover:bg-teal-700 transition">Track</button>
                </form>
            </div>

             <!-- Other Actions Card -->
            <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-200/50">
                <h2 class="text-xl font-bold mb-4 text-gray-900">System Actions</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- 2FA -->
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-2">Two-Factor Authentication</h3>
                        <?php if (!isset($_SESSION['otp'])): ?>
                        <form method="post" action="">
                            <input type="hidden" name="action" value="send_otp">
                            <button type="submit" class="w-full bg-slate-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-slate-700 transition">Send OTP</button>
                        </form>
                        <?php else: ?>
                        <form method="post" action="" class="flex items-center gap-2">
                            <input type="hidden" name="action" value="verify_otp">
                            <input type="text" name="otp" placeholder="Enter OTP" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-500">
                            <button type="submit" class="flex-shrink-0 bg-slate-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-slate-700 transition">Verify</button>
                        </form>
                        <?php endif; ?>
                    </div>
                    <!-- Email Notification -->
                     <div>
                        <h3 class="font-semibold text-gray-700 mb-2">Admin Notification</h3>
                        <form method="post" action="">
                            <input type="hidden" name="action" value="send_email">
                            <button type="submit" class="w-full bg-sky-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-sky-700 transition">Send Email (Simulated)</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-8">
            <!-- Analytics Card -->
            <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-200/50">
                <h2 class="text-xl font-bold mb-4 text-gray-900">Complaint Analytics</h2>
                <div class="h-64">
                    <canvas id="analyticsChart"></canvas>
                </div>
            </div>

            <!-- Chat Card -->
            <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-200/50 flex flex-col h-[28rem]">
                <h2 class="text-xl font-bold mb-4 text-gray-900">Anonymous Chat with Admin</h2>
                <div class="chat-box flex-grow bg-gray-50 rounded-lg p-4 overflow-y-auto space-y-4">
                    <?php if (!empty($chatHistory)): ?>
                        <?php foreach ($chatHistory as $chat): 
                            $isUser = $chat['sender'] === 'User';
                        ?>
                        <div class="flex <?php echo $isUser ? 'justify-end' : 'justify-start'; ?>">
                            <div class="max-w-xs lg:max-w-sm px-4 py-2 rounded-2xl <?php echo $isUser ? 'bg-indigo-500 text-white rounded-br-none' : 'bg-gray-200 text-gray-800 rounded-bl-none'; ?>">
                                <p class="text-sm"><?php echo htmlspecialchars($chat['message']); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center text-gray-500">No messages yet. Start the conversation!</p>
                    <?php endif; ?>
                </div>
                <form method="post" action="" class="mt-4 flex items-center gap-2">
                    <input type="hidden" name="action" value="send_chat">
                    <input type="text" id="chat_message" name="chat_message" placeholder="Type your message..." required class="w-full px-4 py-2.5 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <button type="submit" class="flex-shrink-0 bg-indigo-600 text-white rounded-full p-3 hover:bg-indigo-700 transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
                    </button>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('analyticsChart').getContext('2d');
    const analyticsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo $chartLabelsJson; ?>,
            datasets: [{
                label: 'Complaint Count',
                data: <?php echo $chartDataJson; ?>,
                backgroundColor: 'rgba(79, 70, 229, 0.8)',
                borderColor: 'rgba(79, 70, 229, 1)',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#111827',
                    titleFont: { size: 14 },
                    bodyFont: { size: 12 },
                    padding: 10,
                    cornerRadius: 5
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#e5e7eb' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
</body>
</html>
