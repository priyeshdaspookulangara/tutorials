<?php
require_once 'includes/db.php';
include 'includes/header.php';

$stmt = $conn->prepare("SELECT t.id, tt.name, tt.description FROM topics t JOIN topic_translations tt ON t.id = tt.topic_id WHERE tt.language = ? ORDER BY tt.name ASC");
$stmt->bind_param("s", $lang_to_use);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="row">
    <div class="col-md-12">
        <h1><?php echo trans('welcome_message'); ?></h1>
        <p><?php echo trans('browse_tutorials_message'); ?></p>
    </div>
</div>

<div class="row">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                        <a href="tutorials/topic.php?id=<?php echo $row['id']; ?>" class="btn btn-primary"><?php echo trans('view_tutorials_btn'); ?></a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-md-12">
            <p><?php echo trans('no_topics_found'); ?></p>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
