
<?php
// admin_dashboard.php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

require 'db.php';

// Fetch all complaints
$stmt = $pdo->query("SELECT * FROM complaints ORDER BY created_at DESC");
$complaints = $stmt->fetchAll();

// Calculate analytics data (complaint count per category)
$analytics = array();
foreach ($complaints as $complaint) {
    $cat = $complaint['category'];
    if (isset($analytics[$cat])) {
        $analytics[$cat]++;
    } else {
        $analytics[$cat] = 1;
    }
}
// Simulate email notification if the button is clicked
$notificationMessage = "";
if (isset($_POST['send_notification'])) {
    // Here you could call mail() or any email service
    $notificationMessage = "Email sent to admin!";
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


// Prepare data for Chart.js
$chartLabels = json_encode(array_keys($analytics));
$chartData   = json_encode(array_values($analytics));
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="style.css">
  <style>
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
    th { background: #f7f7f7; }
    a.button { padding: 0px 10px; background: #007BFF; color: #fff; text-decoration: none; border-radius: 3px; margin-top:8px; }
    a.button:hover { background: #0056b3; }
    #chart-container { width: 50%; margin: 30px auto; }
  </style>
</head>
<body>
<div class="container">
  <h1>Admin Dashboard</h1>
  <!-- <a href="index.php">Track Your Complaint</a> -->
  <?php if(empty($complaints)): ?>
    <p>No complaints submitted yet.</p>
  <?php else: ?>
    <table>
      <tr>
        <th>ID</th>
        <th>Category</th>
        <th>Message</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
      <?php foreach($complaints as $complaint): ?>
      <tr>
        <td><?php echo $complaint['id']; ?></td>
        <td><?php echo htmlspecialchars($complaint['category']); ?></td>
        <td><?php echo htmlspecialchars($complaint['message']); ?></td>
        <td><?php echo $complaint['status']; ?></td>
        <td>
          <a class="button" href="update_status.php?id=<?php echo $complaint['id']; ?>&status=In+Progress">In Progress</a>
          <a class="button" href="update_status.php?id=<?php echo $complaint['id']; ?>&status=Resolved">Resolved</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>
  
  <div id="chart-container">
    <h2>Complaint Analytics</h2>
    <canvas id="analyticsChart"></canvas>
  </div>
  
 
    <div class="card">
        <h2><a href="admin.php">Complaint Analytics</a></h2>

        <div class="chart-container">
            <canvas id="analyticsChart"></canvas>
        </div>
    </div>
  <p><a href="logout.php">Logout</a></p>
</div>

 <!-- Load Chart.js from CDN -->
 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Load Chart.js from CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const ctx = document.getElementById('analyticsChart').getContext('2d');
  const analyticsChart = new Chart(ctx, {
      type: 'pie',
      data: {
          labels: <?php echo $chartLabels; ?>,
          datasets: [{
              data: <?php echo $chartData; ?>,
              backgroundColor: ['#0088FE', '#00C49F', '#FFBB28', '#FF8042', '#AA336A', '#33AA99']
          }]
      },
      options: {
          responsive: true,
          plugins: {
              legend: {
                  position: 'bottom',
              }
          }
      }
  });
</script>
</body>
</html>
