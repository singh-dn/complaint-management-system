<?php
// admin_login.php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <h1>Admin Login</h1>
  <form action="admin_auth.php" method="post">
    <label for="admin_id">Admin ID:</label>
    <input type="text" id="admin_id" name="admin_id" required>
    
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>
    
    <button type="submit">Login</button>
  </form>
  <p><a href="index.php">Back to Complaint Form</a></p>
</div>
</body>
</html>
