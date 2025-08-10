<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Anonymous Complaint Form</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <h1>Anonymous Complaint Form</h1>
    <form action="submit_complaint.php" method="post">
      <label for="category">Complaint Category:</label>
      <input type="text" id="category" name="category" placeholder="e.g., Harassment, Workload" required>
      
      <label for="message">Complaint Message:</label>
      <textarea id="message" name="message" placeholder="Describe your issue..." required></textarea>
      
      <button type="submit">Submit Complaint</button>
    </form>
    
    <hr>
    
    <h2>Track Your Complaint</h2>
    <form action="track_complaint.php" method="get">
      <label for="complaint_id">Enter Complaint ID:</label>
      <input type="text" id="complaint_id" name="complaint_id" required>
      <button type="submit">Track Complaint</button>
    </form>
  </div>
</body>
</html>
-