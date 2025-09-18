<?php
require_once '../includes/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php");
    exit();
}

$result = $conn->query("
    SELECT t.id, tt.language, tt.name, tt.description
    FROM topics t
    LEFT JOIN topic_translations tt ON t.id = tt.topic_id
    ORDER BY t.id, tt.language
");
?>

<div class="row">
    <div class="col-md-12">
        <h2><?php echo trans('view_topics'); ?></h2>
        <a href="add_topic.php" class="btn btn-primary mb-3"><?php echo trans('add_new_topic'); ?></a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th><?php echo trans('language'); ?></th>
                    <th><?php echo trans('topic_name'); ?></th>
                    <th><?php echo trans('description'); ?></th>
                    <th><?php echo trans('actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><span class="badge badge-secondary"><?php echo htmlspecialchars($row['language']); ?></span></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td>
                                <a href="edit_topic.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info"><?php echo trans('edit'); ?></a>
                                <a href="delete_topic.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('<?php echo trans('delete_confirm'); ?>');"><?php echo trans('delete'); ?></a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5"><?php echo trans('no_topics_found'); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
