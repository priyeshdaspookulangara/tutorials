<?php
require_once '../includes/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php");
    exit();
}
?>

<div class="row">
    <div class="col-md-12">
        <h2>Admin Panel</h2>
        <p>Welcome to the admin panel. Here you can manage topics and tutorials.</p>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Manage Topics
            </div>
            <div class="card-body">
                <a href="add_topic.php" class="btn btn-primary">Add Topic</a>
                <a href="view_topics.php" class="btn btn-secondary">View Topics</a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Manage Tutorials
            </div>
            <div class="card-body">
                <a href="add_tutorial.php" class="btn btn-primary">Add Tutorial</a>
                <a href="view_tutorials.php" class="btn btn-secondary">View Tutorials</a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
