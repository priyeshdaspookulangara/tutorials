<?php
require_once '../includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php");
    exit();
}

$tutorial_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($tutorial_id > 0) {
    // The database is set up with ON DELETE CASCADE, so pages will be deleted automatically.
    $stmt = $conn->prepare("DELETE FROM tutorials WHERE id = ?");
    $stmt->bind_param("i", $tutorial_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: view_tutorials.php");
exit();
?>
