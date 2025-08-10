<?php
// submit_complaint.php
session_start();
require 'db.php';

// Retrieve form data
$category = isset($_POST['category']) ? trim($_POST['category']) : '';
$message  = isset($_POST['message']) ? trim($_POST['message']) : '';

if (empty($category) || empty($message)) {
    die("Please fill in all fields. <a href='index.html'>Go back</a>");
}

// Generate a unique complaint ID
$complaintId = uniqid('cmp_');

// Prepare the INSERT statement
$stmt = $pdo->prepare("INSERT INTO complaints (id, category, message, status) VALUES (?, ?, ?, 'Open')");
$stmt->execute([$complaintId, $category, $message]);

// Send email notification to admin (adjust email settings as needed)
$to      = "shizzx09@gmail.com";
$subject = "New Complaint Submitted";
$body    = "A new complaint has been submitted.\nComplaint ID: $complaintId\nCategory: $category\nMessage: $message";
$headers = "From: no-reply@gmail.com";

// Uncomment the line below when your PHP mail settings are configured
// mail($to, $subject, $body, $headers);

// Display success message with inline CSS styling
echo "<!DOCTYPE html>
<html>
<head>
  <meta charset='UTF-8'>
  <title>Complaint Submitted</title>
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
    }
    a {
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
  <div class='container'>
    <h2>Complaint Submitted Successfully</h2>
    <p>Your Complaint ID is: <strong>$complaintId</strong></p>
    <p><a href='index.php'>Go Back</a></p>
  </div>
</body>
</html>";
?>
