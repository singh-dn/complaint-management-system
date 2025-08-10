<?php
// admin_auth.php
session_start();

$admin_id = isset($_POST['admin_id']) ? trim($_POST['admin_id']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

if ($admin_id === 'admin' && $password === 'password') {
    $_SESSION['is_admin'] = true;
    header("Location: admin_dashboard.php");
    exit;
} else {
    echo "<p>Invalid credentials. <a href='admin_login.php'>Try Again</a></p>";
}
?>
