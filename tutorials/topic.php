<?php
require_once '../includes/db.php';
include '../includes/header.php';

$topic_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($topic_id <= 0) {
    header("Location: /");
    exit();
}

$stmt_topic = $conn->prepare("SELECT tt.name, tt.description FROM topics t JOIN topic_translations tt ON t.id = tt.topic_id WHERE t.id = ? AND tt.language = ?");
$stmt_topic->bind_param("is", $topic_id, $lang_to_use);
$stmt_topic->execute();
$result_topic = $stmt_topic->get_result();
$topic = $result_topic->fetch_assoc();
$stmt_topic->close();

if (!$topic) {
    header("Location: /");
    exit();
}

$stmt_tutorials = $conn->prepare("
    SELECT t.id, t.is_premium, tt.title
    FROM tutorials t
    JOIN tutorial_translations tt ON t.id = tt.tutorial_id
    WHERE t.topic_id = ? AND tt.language = ?
    ORDER BY tt.title ASC
");
$stmt_tutorials->bind_param("is", $topic_id, $lang_to_use);
$stmt_tutorials->execute();
$result_tutorials = $stmt_tutorials->get_result();
?>

<div class="row">
    <div class="col-md-12">
        <h2><?php echo sprintf(trans('tutorials_for_topic'), htmlspecialchars($topic['name'])); ?></h2>
        <p><?php echo htmlspecialchars($topic['description']); ?></p>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="list-group">
            <?php if ($result_tutorials->num_rows > 0): ?>
                <?php while ($row = $result_tutorials->fetch_assoc()): ?>
                    <a href="lesson.php?id=<?php echo $row['id']; ?>" class="list-group-item list-group-item-action">
                        <?php echo htmlspecialchars($row['title']); ?>
                        <?php if ($row['is_premium']): ?>
                            <span class="badge badge-warning ml-2"><?php echo trans('premium_badge'); ?></span>
                        <?php endif; ?>
                    </a>
                <?php endwhile; ?>
            <?php else: ?>
                <p><?php echo trans('no_tutorials_found'); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
