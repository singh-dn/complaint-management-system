<?php
// track_complaint.php
require 'db.php';

$complaintId = isset($_GET['complaint_id']) ? trim($_GET['complaint_id']) : '';

// --- Helper function to determine status badge color ---
function getStatusBadge($status) {
    $status = strtolower($status);
    switch ($status) {
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
  <title>Track Complaint Status</title>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      /* Consistent background pattern */
      background-color: #f8fafc;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='28' height='49' viewBox='0 0 28 49'%3E%3Cg fill-rule='evenodd'%3E%3Cg id='hexagons' fill='%23e2e8f0' fill-opacity='0.4' fill-rule='nonzero'%3E%3Cpath d='M13.99 9.25l13 7.5v15l-13 7.5L1 31.75v-15l12.99-7.5zM3 17.9v12.7l10.99 6.34 11-6.35V17.9l-11-6.34L3 17.9zM0 15l12.99-7.5L26 15v18.5l-13 7.5L0 33.5V15z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
  </style>
</head>
<body class="flex items-center justify-center min-h-screen">

  <div class="bg-white p-8 sm:p-10 rounded-2xl shadow-xl max-w-lg w-full border border-gray-200/50">
    
    <?php if (empty($complaintId)): ?>
    <!-- STATE 1: No Complaint ID Entered -->
    <div class="text-center">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mb-5">
            <svg class="h-8 w-8 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900">Missing Information</h2>
        <p class="text-gray-600 mt-3">Please enter a Complaint ID to track its status.</p>
    </div>

    <?php else: 
        $stmt = $pdo->prepare("SELECT status FROM complaints WHERE id = ?");
        $stmt->execute([$complaintId]);
        $complaint = $stmt->fetch();

        if (!$complaint): ?>
        <!-- STATE 2: Complaint Not Found -->
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-5">
                <svg class="h-8 w-8 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Complaint Not Found</h2>
            <p class="text-gray-600 mt-3">No complaint was found with the ID:</p>
            <div class="mt-2 bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-3">
                <strong class="font-mono text-red-600"><?php echo htmlspecialchars($complaintId); ?></strong>
            </div>
        </div>

        <?php else: 
            $status = htmlspecialchars($complaint['status']);
            $badgeClasses = getStatusBadge($status);
        ?>
        <!-- STATE 3: Complaint Found -->
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-indigo-100 mb-5">
                <svg class="h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Complaint Status</h2>
            <p class="text-gray-600 mt-3">Showing status for Complaint ID:</p>
            <div class="mt-2 bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-3">
                <strong class="font-mono text-gray-800"><?php echo htmlspecialchars($complaintId); ?></strong>
            </div>
        </div>

        <div class="mt-6 border-t border-gray-200 pt-6">
            <dl class="space-y-4">
                <div class="flex justify-between items-center">
                    <dt class="text-md font-medium text-gray-600">Current Status:</dt>
                    <dd class="text-md font-semibold px-3 py-1 rounded-full <?php echo $badgeClasses; ?>">
                        <?php echo $status; ?>
                    </dd>
                </div>
            </dl>
        </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Common "Go Back" Button -->
    <div class="mt-8 text-center">
      <a href="index.php" class="inline-block w-full sm:w-auto bg-gray-700 text-white py-3 px-6 rounded-lg font-semibold hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-300">
        &larr; Go Back
      </a>
    </div>

  </div>

</body>
</html>
