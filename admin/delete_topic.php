<?php
require_once '../includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php");
    exit();
}

$topic_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($topic_id > 0) {
    $stmt = $conn->prepare("DELETE FROM topics WHERE id = ?");
    $stmt->bind_param("i", $topic_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: view_topics.php");
exit();
?>
