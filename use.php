<?php
session_start();
require 'db.php'; // Include your database connection

// Initialize output message and chat history
$messageOutput = "";
$chatHistory = isset($_SESSION['chatHistory']) ? $_SESSION['chatHistory'] : [];

// Process form submissions using a hidden "action" field
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action === "submit_complaint") {
    $cat = trim($_POST['category']);
    $msg = trim($_POST['message']);
    if (empty($cat) || empty($msg)) {
        $messageOutput = "Please fill in all fields.";
    } else {
        $complaintId = uniqid("cmp_");
        $stmt = $pdo->prepare("INSERT INTO complaints (id, category, message, status) VALUES (?, ?, ?, 'Pending')");
        $stmt->execute([$complaintId, $cat, $msg]);
        $messageOutput = "Complaint submitted successfully. Your Complaint ID: " . $complaintId;
    }
}

if ($action === "track_complaint") {
    $trackId = trim($_POST['track_id']);
    if (empty($trackId)) {
        $messageOutput = "Please enter a Complaint ID.";
    } else {
        $stmt = $pdo->prepare("SELECT status FROM complaints WHERE id = ?");
        $stmt->execute([$trackId]);
        $complaint = $stmt->fetch();
        if ($complaint) {
            $messageOutput = "Complaint ID: " . htmlspecialchars($trackId) . " - Status: " . htmlspecialchars($complaint['status']);
        } else {
            $messageOutput = "No complaint found with ID: " . htmlspecialchars($trackId);
        }
    }
}

if ($action === "send_email") {
    // Simulate sending an email notification
    // Use mail() if configured, e.g.: mail($to, $subject, $body, $headers);
    $messageOutput = "Email sent to admin!";
}

if ($action === "send_chat") {
    $chatMsg = trim($_POST['chat_message']);
    if (!empty($chatMsg)) {
        $_SESSION['chatHistory'][] = ["sender" => "User", "message" => $chatMsg];
        $chatHistory = $_SESSION['chatHistory'];
    }
}

if ($action === "send_otp") {
    // Generate an OTP (for demo purposes, fixed value)
    $_SESSION['otp'] = "123456";
    $messageOutput = "OTP sent to your registered contact.";
}

if ($action === "verify_otp") {
    $userOtp = trim($_POST['otp']);
    if (isset($_SESSION['otp']) && $userOtp === $_SESSION['otp']) {
        $messageOutput = "2FA Verified Successfully";
        unset($_SESSION['otp']);
    } else {
        $messageOutput = "Invalid OTP";
    }
}

// Fetch analytics data (group complaints by category)
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
<html>
<head>
  <meta charset="UTF-8">
  <title>Anonymous Complaint & Feedback System</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f2f2f2;
      margin: 0;
      padding: 20px;
    }
    .container {
      max-width: 900px;
      margin: 0 auto;
    }
    h1 {
      text-align: center;
      color: #333;
    }
    .card {
      background: #fff;
      padding: 20px;
      margin-bottom: 20px;
      border-radius: 5px;
      box-shadow: 0 0 10px #ccc;
    }
    .card h2 {
      margin-top: 0;
      color: #007BFF;
    }
    .form-group {
      margin-bottom: 15px;
    }
    .form-group label {
      font-weight: bold;
      display: block;
      margin-bottom: 5px;
    }
    .form-group input[type="text"],
    .form-group textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 3px;
    }
    button {
      background: #007BFF;
      color: #fff;
      border: none;
      padding: 10px 20px;
      border-radius: 3px;
      cursor: pointer;
    }
    button:hover {
      background: #0056b3;
    }
    .chat-box {
      border: 1px solid #ccc;
      padding: 10px;
      height: 150px;
      overflow-y: auto;
      background: #fafafa;
    }
    .message {
      margin-bottom: 10px;
    }
    .message strong {
      color: #007BFF;
    }
    .output-message {
      text-align: center;
      color: green;
      margin-bottom: 20px;
    }
    .otp-container, .chat-container {
      display: flex;
      gap: 10px;
      margin-top: 10px;
    }
    .chart-container {
      width: 100%;
      height: 300px;
    }
  </style>
  <!-- Load Chart.js from CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container">
  <h1>Anonymous Complaint & Feedback System</h1>
  
  <?php if (!empty($messageOutput)): ?>
    <div class="output-message"><?php echo $messageOutput; ?></div>
  <?php endif; ?>
  
  <!-- Complaint Submission Form -->
  <div class="card">
    <h2>Submit Complaint</h2>
    <form method="post" action="">
      <input type="hidden" name="action" value="submit_complaint">
      <div class="form-group">
        <label for="category">Complaint Category:</label>
        <input type="text" id="category" name="category" placeholder="e.g., HR, IT" required>
      </div>
      <div class="form-group">
        <label for="message">Complaint Message:</label>
        <textarea id="message" name="message" placeholder="Describe your issue..." required></textarea>
      </div>
      <button type="submit">Submit Complaint</button>
    </form>
  </div>
  
  <!-- Complaint Analytics -->
  <div class="card">
    <h2>Complaint Analytics</h2>
    <div class="chart-container">
      <canvas id="analyticsChart"></canvas>
    </div>
  </div>
  
  <!-- Complaint Status Tracker -->
  <div class="card">
    <h2>Complaint Status Tracker</h2>
    <form method="post" action="">
      <input type="hidden" name="action" value="track_complaint">
      <div class="form-group">
        <label for="track_id">Enter Complaint ID:</label>
        <input type="text" id="track_id" name="track_id" required>
      </div>
      <button type="submit">Track Complaint</button>
    </form>
  </div>
  
  <!-- Email Notifications -->
  <div class="card">
    <h2>Email Notifications (Simulated)</h2>
    <form method="post" action="">
      <input type="hidden" name="action" value="send_email">
      <button type="submit">Send Notification</button>
    </form>
  </div>
  
  <!-- Anonymous Chat with Admin -->
  <div class="card">
    <h2>Anonymous Chat with Admin</h2>
    <div class="chat-box">
      <?php if (!empty($chatHistory)): ?>
        <?php foreach ($chatHistory as $chat): ?>
          <div class="message"><strong><?php echo htmlspecialchars($chat['sender']); ?>:</strong> <?php echo htmlspecialchars($chat['message']); ?></div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No messages yet.</p>
      <?php endif; ?>
    </div>
    <form method="post" action="">
      <input type="hidden" name="action" value="send_chat">
      <div class="form-group">
        <label for="chat_message">Type your message:</label>
        <input type="text" id="chat_message" name="chat_message" placeholder="Your message..." required>
      </div>
      <button type="submit">Send</button>
    </form>
  </div>
  
  <!-- Two-Factor Authentication (2FA) -->
  <div class="card">
    <h2>Two-Factor Authentication (2FA)</h2>
    <?php if (!isset($_SESSION['otp'])): ?>
      <form method="post" action="">
        <input type="hidden" name="action" value="send_otp">
        <button type="submit">Send OTP</button>
      </form>
    <?php else: ?>
      <form method="post" action="">
        <input type="hidden" name="action" value="verify_otp">
        <div class="otp-container">
          <input type="text" name="otp" placeholder="Enter OTP" required>
          <button type="submit">Verify OTP</button>
        </div>
      </form>
    <?php endif; ?>
  </div>
  
</div>

<script>
  // Render the Bar Chart using Chart.js
  const ctx = document.getElementById('analyticsChart').getContext('2d');
  const analyticsChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: <?php echo $chartLabelsJson; ?>,
      datasets: [{
        label: 'Complaint Count',
        data: <?php echo $chartDataJson; ?>,
        backgroundColor: '#8884d8'
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false }
      }
    }
  });
</script>
</body>
</html>
