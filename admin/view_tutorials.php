<?php
require_once '../includes/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php");
    exit();
}

$result = $conn->query("SELECT t.id, t.title, t.is_premium, p.name AS topic_name FROM tutorials t JOIN topics p ON t.topic_id = p.id ORDER BY p.name, t.title ASC");
?>

<div class="row">
    <div class="col-md-12">
        <h2>View Tutorials</h2>
        <a href="add_tutorial.php" class="btn btn-primary mb-3">Add New Tutorial</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tutorial Title</th>
                    <th>Topic</th>
                    <th>Premium</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['topic_name']); ?></td>
                            <td><?php echo $row['is_premium'] ? 'Yes' : 'No'; ?></td>
                            <td>
                                <a href="edit_tutorial.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">Edit</a>
                                <a href="delete_tutorial.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this tutorial?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No tutorials found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
