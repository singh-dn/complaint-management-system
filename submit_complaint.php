<?php
// submit_complaint.php
session_start();
require 'db.php';

// Retrieve form data
$category = isset($_POST['category']) ? trim($_POST['category']) : '';
$message  = isset($_POST['message']) ? trim($_POST['message']) : '';

if (empty($category) || empty($message)) {
    // A simple, clean error message
    die("
        <div style='font-family: sans-serif; text-align: center; padding: 40px;'>
            <h2>Error</h2>
            <p>Please fill in all fields.</p>
            <a href='index.php' style='color: #3b82f6;'>Go back</a>
        </div>
    ");
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
$headers = "From: no-reply@yourdomain.com";

// Uncomment the line below when your PHP mail settings are configured
// mail($to, $subject, $body, $headers);

// Display success message with Tailwind CSS
echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Complaint Submitted Successfully</title>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      /* Using the same background as the form page for consistency */
      background-color: #f8fafc;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='28' height='49' viewBox='0 0 28 49'%3E%3Cg fill-rule='evenodd'%3E%3Cg id='hexagons' fill='%23e2e8f0' fill-opacity='0.4' fill-rule='nonzero'%3E%3Cpath d='M13.99 9.25l13 7.5v15l-13 7.5L1 31.75v-15l12.99-7.5zM3 17.9v12.7l10.99 6.34 11-6.35V17.9l-11-6.34L3 17.9zM0 15l12.99-7.5L26 15v18.5l-13 7.5L0 33.5V15z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
  </style>
</head>
<body class="flex items-center justify-center min-h-screen">

  <div class="bg-white p-8 sm:p-10 rounded-2xl shadow-xl text-center max-w-lg w-full border border-gray-200/50">
    <!-- Success Icon -->
    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-5">
      <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
      </svg>
    </div>

    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Complaint Submitted Successfully!</h2>
    <p class="text-gray-600 mt-3">Please save your Complaint ID to track its status later.</p>
    
    <div class="mt-6">
      <p class="text-sm text-gray-500">Your Complaint ID is:</p>
      <div class="mt-2 bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-4">
        <strong class="text-lg sm:text-xl font-mono text-indigo-600 tracking-wider">$complaintId</strong>
      </div>
    </div>

    <div class="mt-8">
      <a href="index.php" class="inline-block w-full sm:w-auto bg-indigo-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300">
        File Another Complaint
      </a>
    </div>
  </div>

</body>
</html>
HTML;
?>
