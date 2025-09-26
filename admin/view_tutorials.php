<?php
require_once '../includes/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php");
    exit();
}

// The main query now needs to fetch the default language title for display
$stmt = $conn->prepare("
    SELECT t.id, t.is_premium, tt.title, topic_trans.name as topic_name
    FROM tutorials t
    LEFT JOIN tutorial_translations tt ON t.id = tt.tutorial_id AND tt.language = ?
    LEFT JOIN topic_translations topic_trans ON t.topic_id = topic_trans.topic_id AND topic_trans.language = ?
    ORDER BY t.id
");
$stmt->bind_param("ss", $default_lang, $default_lang);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>
<div class="row">
    <div class="col-md-12">
        <h2><?php echo trans('view_tutorials'); ?></h2>
        <a href="add_tutorial.php" class="btn btn-primary mb-3"><?php echo trans('add_new_tutorial'); ?></a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th><?php echo trans('tutorial_title'); ?> (<?php echo $default_lang; ?>)</th>
                    <th><?php echo trans('topic'); ?></th>
                    <th><?php echo trans('premium'); ?></th>
                    <th><?php echo trans('actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['topic_name']); ?></td>
                            <td><?php echo $row['is_premium'] ? trans('yes') : trans('no'); ?></td>
                            <td>
                                <a href="edit_tutorial.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info"><?php echo trans('edit'); ?></a>
                                <a href="delete_tutorial.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('<?php echo trans('delete_confirm'); ?>');"><?php echo trans('delete'); ?></a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5"><?php echo trans('no_tutorials_found'); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
