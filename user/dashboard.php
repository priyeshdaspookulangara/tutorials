<?php
require_once '../includes/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<div class="row">
    <div class="col-md-12">
        <h2><?php echo sprintf(trans('welcome_user'), htmlspecialchars($_SESSION['username'])); ?></h2>
        <p><?php echo trans('dashboard_welcome'); ?></p>
        <a href="logout.php" class="btn btn-danger"><?php echo trans('logout'); ?></a>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <h3><?php echo trans('premium_tutorials'); ?></h3>
        <div class="list-group">
            <?php
            $stmt = $conn->prepare("
                SELECT t.id, tt.title
                FROM tutorials t
                JOIN tutorial_translations tt ON t.id = tt.tutorial_id
                WHERE t.is_premium = 1 AND tt.language = ?
            ");
            $stmt->bind_param("s", $lang_to_use);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<a href="/tutorials/lesson.php?id=' . $row['id'] . '" class="list-group-item list-group-item-action">' . htmlspecialchars($row['title']) . '</a>';
                }
            } else {
                echo '<p>' . trans('no_premium_tutorials') . '</p>';
            }

            $stmt->close();
            ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
