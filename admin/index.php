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
        <h2><?php echo trans('admin_panel'); ?></h2>
        <p><?php echo trans('admin_welcome'); ?></p>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <?php echo trans('manage_topics'); ?>
            </div>
            <div class="card-body">
                <a href="add_topic.php" class="btn btn-primary"><?php echo trans('add_topic'); ?></a>
                <a href="view_topics.php" class="btn btn-secondary"><?php echo trans('view_topics'); ?></a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <?php echo trans('manage_tutorials'); ?>
            </div>
            <div class="card-body">
                <a href="add_tutorial.php" class="btn btn-primary"><?php echo trans('add_tutorial'); ?></a>
                <a href="view_tutorials.php" class="btn btn-secondary"><?php echo trans('view_tutorials'); ?></a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
