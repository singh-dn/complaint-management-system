<?php
// update_status.php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    require 'db.php';
    $complaintId = $_GET['id'];
    $newStatus   = $_GET['status'];

    $stmt = $pdo->prepare("UPDATE complaints SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $complaintId]);
}
header("Location: admin_dashboard.php");
exit;
?>
