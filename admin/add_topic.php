<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php");
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name']);
    $description = sanitize_input($_POST['description']);

    if (empty($name)) {
        $errors[] = 'Topic name is required.';
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO topics (name, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $description);

        if ($stmt->execute()) {
            header("Location: view_topics.php");
            exit();
        } else {
            $errors[] = "Failed to add topic. Please try again.";
        }

        $stmt->close();
    }
}
?>

<div class="row">
    <div class="col-md-12">
        <h2>Add Topic</h2>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form action="add_topic.php" method="post">
            <div class="form-group">
                <label for="name">Topic Name</label>
                <input type="text" name="name" id="name" class="form-control" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control"><?php echo isset($description) ? htmlspecialchars($description) : ''; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Add Topic</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
