<?php
require_once '../includes/db.php';
include '../includes/header.php';

$topic_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($topic_id <= 0) {
    header("Location: /");
    exit();
}

$stmt_topic = $conn->prepare("SELECT name, description FROM topics WHERE id = ?");
$stmt_topic->bind_param("i", $topic_id);
$stmt_topic->execute();
$result_topic = $stmt_topic->get_result();
$topic = $result_topic->fetch_assoc();
$stmt_topic->close();

if (!$topic) {
    header("Location: /");
    exit();
}

$stmt_tutorials = $conn->prepare("SELECT id, title, is_premium FROM tutorials WHERE topic_id = ? ORDER BY title ASC");
$stmt_tutorials->bind_param("i", $topic_id);
$stmt_tutorials->execute();
$result_tutorials = $stmt_tutorials->get_result();
?>

<div class="row">
    <?php include '../includes/sidebar.php'; ?>

    <div class="col-md-9">
        <h2><?php echo htmlspecialchars($topic['name']); ?> Tutorials</h2>
        <p><?php echo htmlspecialchars($topic['description']); ?></p>

        <div class="list-group">
            <?php if ($result_tutorials->num_rows > 0): ?>
                <?php while ($row = $result_tutorials->fetch_assoc()): ?>
                    <a href="lesson.php?id=<?php echo $row['id']; ?>" class="list-group-item list-group-item-action">
                        <?php echo htmlspecialchars($row['title']); ?>
                        <?php if ($row['is_premium']): ?>
                            <span class="badge badge-warning ml-2">Premium</span>
                        <?php endif; ?>
                    </a>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No tutorials found for this topic.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
