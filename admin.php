<?php
session_start();
require 'db.php';

// Optional: Check if admin is logged in
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Fetch analytics data: group complaints by category
$stmt = $pdo->query("SELECT category, COUNT(*) as count FROM complaints GROUP BY category");
$analyticsData = $stmt->fetchAll();
$chartLabels = [];
$chartData = [];
foreach ($analyticsData as $data) {
    $chartLabels[] = $data['category'];
    $chartData[] = $data['count'];
}
$chartLabelsJson = json_encode($chartLabels);
$chartDataJson   = json_encode($chartData);

// Fetch full complaint details for the list
$stmt2 = $pdo->query("SELECT * FROM complaints ORDER BY created_at DESC");
$complaints = $stmt2->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
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
        table {
          width: 100%;
          border-collapse: collapse;
          margin-top: 20px;
        }
        table, th, td {
          border: 1px solid #ccc;
        }
        th, td {
          padding: 10px;
          text-align: left;
        }
        th {
          background-color: #f7f7f7;
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
        .chart-container {
          width: 100%;
          height: 300px;
          margin-top: 20px;
        }
        .output {
          text-align: center;
          color: green;
          margin-bottom: 20px;
        }
        /* Additional CSS for chat system */
        .chat-container {
          border: 1px solid #ccc;
          border-radius: 5px;
          background: #fff;
          padding: 10px;
          margin-top: 20px;
          max-height: 400px;
          overflow-y: auto;
        }
        .chat-message {
          margin-bottom: 10px;
        }
        .chat-message span {
          display: inline-block;
          padding: 5px 10px;
          border-radius: 15px;
        }
        .chat-message.user span {
          background: #007BFF;
          color: #fff;
        }
        .chat-message.admin span {
          background: #ccc;
          color: #333;
        }
        .chat-input {
          margin-top: 10px;
          display: flex;
        }
        .chat-input input {
          flex: 1;
          padding: 10px;
          border: 1px solid #ccc;
          border-radius: 5px 0 0 5px;
        }
        .chat-input button {
          padding: 10px 20px;
          border: none;
          background: #007BFF;
          color: #fff;
          border-radius: 0 5px 5px 0;
          cursor: pointer;
        }
        .chat-input button:hover {
          background: #0056b3;
        }
    </style>
    <!-- Load Chart.js from CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container">
    <h1>Admin Dashboard</h1>
    
    <!-- Section 1: Complaint Analytics -->
    <div class="card">
        <a href="admin_dashboard.php">Admin Dashboard</a>
        <h2>Complaint Analytics</h2>
        <div class="chart-container">
            <canvas id="analyticsChart"></canvas>
        </div>
    </div>
    
    <!-- Section 2: Complaints List -->
    <div class="card">
        <h2>Complaints List</h2>
        <?php if (empty($complaints)): ?>
            <p>No complaints submitted yet.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Category</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Submitted At</th>
                </tr>
                <?php foreach ($complaints as $comp): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($comp['id']); ?></td>
                        <td><?php echo htmlspecialchars($comp['category']); ?></td>
                        <td><?php echo htmlspecialchars($comp['message']); ?></td>
                        <td><?php echo htmlspecialchars($comp['status']); ?></td>
                        <td><?php echo htmlspecialchars($comp['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
    
    <!-- Section 3: Anonymous Chat System -->
    <div class="card">
        <h2>Anonymous Chat System</h2>
        <div class="chat-container" id="chatContainer">
            <!-- Chat messages will be appended here -->
        </div>
        <div class="chat-input">
            <input type="text" id="chatMessage" placeholder="Type your message..." />
            <button onclick="sendChat()">Send</button>
        </div>
    </div>
</div>

<script>
  // Initialize the analytics chart using Chart.js
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

  // Anonymous Chat System Logic
  function sendChat() {
    const chatInput = document.getElementById('chatMessage');
    const chatContainer = document.getElementById('chatContainer');
    const message = chatInput.value.trim();
    if (message !== '') {
      // Append user message to chat container
      const messageDiv = document.createElement('div');
      messageDiv.className = 'chat-message user';
      const span = document.createElement('span');
      span.textContent = message;
      messageDiv.appendChild(span);
      chatContainer.appendChild(messageDiv);
      chatInput.value = '';
      // Auto-scroll to the bottom
      chatContainer.scrollTop = chatContainer.scrollHeight;
      
      // Simulate admin response after 1 second
      setTimeout(function() {
        const adminMessageDiv = document.createElement('div');
        adminMessageDiv.className = 'chat-message admin';
        const adminSpan = document.createElement('span');
        adminSpan.textContent = 'Admin: We have received your message.';
        adminMessageDiv.appendChild(adminSpan);
        chatContainer.appendChild(adminMessageDiv);
        // Auto-scroll to the bottom
        chatContainer.scrollTop = chatContainer.scrollHeight;
      }, 1000);
    }
  }
</script>
</body>
</html>

