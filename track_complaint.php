<?php
// track_complaint.php
require 'db.php';

$complaintId = isset($_GET['complaint_id']) ? trim($_GET['complaint_id']) : '';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Track Complaint</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f2f2f2;
      margin: 0;
      padding: 0;
    }
    .container {
      width: 50%;
      margin: 50px auto;
      background: #fff;
      padding: 20px;
      border-radius: 5px;
      box-shadow: 0 0 10px #ccc;
      text-align: center;
    }
    h2 {
      color: #007BFF;
      margin-bottom: 20px;
    }
    p {
      font-size: 16px;
      color: #333;
    }
    a {
      display: inline-block;
      margin-top: 20px;
      color: #007BFF;
      text-decoration: none;
      font-weight: bold;
    }
    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <?php
    if (empty($complaintId)) {
        echo "<p>Please enter a Complaint ID. <a href='index.html'>Go back</a></p>";
    } else {
        $stmt = $pdo->prepare("SELECT status FROM complaints WHERE id = ?");
        $stmt->execute([$complaintId]);
        $complaint = $stmt->fetch();
        if (!$complaint) {
            echo "<p>No complaint found with ID: " . htmlspecialchars($complaintId) . "</p>";
        } else {
            echo "<h2>Complaint Status</h2>";
            echo "<p>Complaint ID: <strong>" . htmlspecialchars($complaintId) . "</strong></p>";
            echo "<p>Status: <strong>" . htmlspecialchars($complaint['status']) . "</strong></p>";
        }
    }
    ?>
    <a href="index.php">Go Back</a>
  </div>
</body>
</html>
