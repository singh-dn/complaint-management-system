<?php
// admin_login.php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login</title>
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

  <div class="w-full max-w-md">
    <div class="bg-white p-8 sm:p-10 rounded-2xl shadow-xl border border-gray-200/50">
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Admin Login</h1>
        <p class="text-gray-600 mt-2">Access the Complaint Management Dashboard.</p>
      </div>
      
      <form action="admin_auth.php" method="post" class="space-y-6">
        <div>
          <label for="admin_id" class="block text-sm font-medium text-gray-700 mb-2">Admin ID</label>
          <input type="text" id="admin_id" name="admin_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out" placeholder="Enter your admin ID">
        </div>
        
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
          <input type="password" id="password" name="password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out" placeholder="••••••••">
        </div>
        
        <div>
          <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300">
            Sign In
          </button>
        </div>
      </form>
    </div>
    
    <p class="text-center mt-6 text-sm text-gray-600">
      <a href="index.php" class="font-medium text-indigo-600 hover:text-indigo-500">
        &larr; Back to Complaint Form
      </a>
    </p>
  </div>

</body>
</html>
