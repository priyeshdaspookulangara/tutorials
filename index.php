<?php
require_once 'includes/db.php';
include 'includes/header.php';

$result = $conn->query("SELECT * FROM topics ORDER BY name ASC");
?>

<div class="row">
    <?php include 'includes/sidebar.php'; ?>

    <div class="col-md-9">
        <h1>Welcome to the Tutorial System</h1>
        <p>Browse our tutorials by topic below. Select a topic from the sidebar to get started.</p>

        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php mysqli_data_seek($result, 0); // Reset pointer to loop again if needed, though it's a new query in sidebar ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                                <a href="tutorials/topic.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">View Tutorials</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-md-12">
                    <p>No topics found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
