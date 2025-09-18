<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php");
    exit();
}

$errors = [];
$topic_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($topic_id <= 0) {
    header("Location: view_topics.php");
    exit();
}

$stmt = $conn->prepare("SELECT name, description FROM topics WHERE id = ?");
$stmt->bind_param("i", $topic_id);
$stmt->execute();
$result = $stmt->get_result();
$topic = $result->fetch_assoc();

if (!$topic) {
    header("Location: view_topics.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name']);
    $description = sanitize_input($_POST['description']);

    if (empty($name)) {
        $errors[] = 'Topic name is required.';
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE topics SET name = ?, description = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $description, $topic_id);

        if ($stmt->execute()) {
            header("Location: view_topics.php");
            exit();
        } else {
            $errors[] = "Failed to update topic. Please try again.";
        }

        $stmt->close();
    }
}
?>

<div class="row">
    <div class="col-md-12">
        <h2>Edit Topic</h2>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form action="edit_topic.php?id=<?php echo $topic_id; ?>" method="post">
            <div class="form-group">
                <label for="name">Topic Name</label>
                <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($topic['name']); ?>">
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control"><?php echo htmlspecialchars($topic['description']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Topic</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
