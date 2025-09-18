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
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p>This is your dashboard, where you can access premium content and manage your account.</p>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <h3>Premium Tutorials</h3>
        <div class="list-group">
            <?php
            $stmt = $conn->prepare("SELECT id, title FROM tutorials WHERE is_premium = 1");
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<a href="/tutorials/lesson.php?id=' . $row['id'] . '" class="list-group-item list-group-item-action">' . htmlspecialchars($row['title']) . '</a>';
                }
            } else {
                echo '<p>No premium tutorials available at the moment.</p>';
            }

            $stmt->close();
            ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
