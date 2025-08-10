<?php
// complaint_analytics.php
// This file assumes that db.php has already been required in the parent file.
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
?>
<div class="card">
    <h2>Complaint Analytics</h2>
    <div class="chart-container">
        <canvas id="analyticsChart"></canvas>
    </div>
</div>
<script>
  const ctx = document.getElementById('analyticsChart').getContext('2d');
  const analyticsChart = new Chart(ctx, {
      type: 'pie',
      data: {
          labels: <?php echo $chartLabelsJson; ?>,
          datasets: [{
              data: <?php echo $chartDataJson; ?>,
              backgroundColor: ['#0088FE', '#00C49F', '#FFBB28', '#FF8042', '#AA336A', '#33AA99']
          }]
      },
      options: {
          responsive: true,
          plugins: {
              legend: {
                  position: 'bottom'
              }
          }
      }
  });
</script>
