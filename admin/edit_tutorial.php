<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php");
    exit();
}

$errors = [];
$tutorial_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($tutorial_id <= 0) {
    header("Location: view_tutorials.php");
    exit();
}

$stmt = $conn->prepare("SELECT topic_id, title, is_premium FROM tutorials WHERE id = ?");
$stmt->bind_param("i", $tutorial_id);
$stmt->execute();
$result = $stmt->get_result();
$tutorial = $result->fetch_assoc();
$stmt->close();

if (!$tutorial) {
    header("Location: view_tutorials.php");
    exit();
}

$pages_result = $conn->query("SELECT id, page_number, title, content FROM tutorial_pages WHERE tutorial_id = $tutorial_id ORDER BY page_number ASC");
$pages = [];
while ($row = $pages_result->fetch_assoc()) {
    $pages[] = $row;
}

$topics = $conn->query("SELECT id, name FROM topics ORDER BY name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize_input($_POST['title']);
    $topic_id = (int)$_POST['topic_id'];
    $is_premium = isset($_POST['is_premium']) ? 1 : 0;
    $posted_pages = $_POST['pages'];

    if (empty($title)) {
        $errors[] = 'Tutorial title is required.';
    }

    if ($topic_id <= 0) {
        $errors[] = 'Please select a topic.';
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE tutorials SET topic_id = ?, title = ?, is_premium = ? WHERE id = ?");
        $stmt->bind_param("isii", $topic_id, $title, $is_premium, $tutorial_id);

        if ($stmt->execute()) {
            $stmt->close();

            $conn->query("DELETE FROM tutorial_pages WHERE tutorial_id = $tutorial_id");
            $stmt_page = $conn->prepare("INSERT INTO tutorial_pages (tutorial_id, page_number, title, content) VALUES (?, ?, ?, ?)");
            foreach ($posted_pages as $index => $page) {
                $page_number = $index + 1;
                $page_title = sanitize_input($page['title']);
                $page_content = sanitize_input($page['content']);
                $stmt_page->bind_param("iiss", $tutorial_id, $page_number, $page_title, $page_content);
                $stmt_page->execute();
            }
            $stmt_page->close();

            header("Location: view_tutorials.php");
            exit();
        } else {
            $errors[] = "Failed to update tutorial. Please try again.";
        }
    }
}
?>

<div class="row">
    <div class="col-md-12">
        <h2>Edit Tutorial</h2>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form action="edit_tutorial.php?id=<?php echo $tutorial_id; ?>" method="post">
            <div class="form-group">
                <label for="title">Tutorial Title</label>
                <input type="text" name="title" id="title" class="form-control" value="<?php echo htmlspecialchars($tutorial['title']); ?>">
            </div>
            <div class="form-group">
                <label for="topic_id">Topic</label>
                <select name="topic_id" id="topic_id" class="form-control">
                    <option value="">Select a Topic</option>
                    <?php while ($row = $topics->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>" <?php if ($row['id'] == $tutorial['topic_id']) echo 'selected'; ?>><?php echo htmlspecialchars($row['name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" name="is_premium" id="is_premium" class="form-check-input" value="1" <?php if ($tutorial['is_premium']) echo 'checked'; ?>>
                <label for="is_premium" class="form-check-label">Premium Content</label>
            </div>

            <hr>

            <h4>Tutorial Pages</h4>
            <div id="pages-container">
                <?php foreach ($pages as $index => $page): ?>
                <div class="page-item card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-sm btn-danger remove-page mb-2">Remove</button>
                        </div>
                        <div class="form-group">
                            <label>Page Title</label>
                            <input type="text" name="pages[<?php echo $index; ?>][title]" class="form-control" value="<?php echo htmlspecialchars($page['title']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Page Content</label>
                            <textarea name="pages[<?php echo $index; ?>][content]" class="form-control" rows="5"><?php echo htmlspecialchars($page['content']); ?></textarea>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" id="add-page" class="btn btn-secondary">Add Page</button>
            <hr>

            <button type="submit" class="btn btn-primary">Update Tutorial</button>
        </form>
    </div>
</div>

<script>
    document.getElementById('add-page').addEventListener('click', function() {
        const container = document.getElementById('pages-container');
        const index = container.children.length;
        const newItem = document.createElement('div');
        newItem.classList.add('page-item', 'card', 'mb-3');
        newItem.innerHTML = `
            <div class="card-body">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-sm btn-danger remove-page mb-2">Remove</button>
                </div>
                <div class="form-group">
                    <label>Page Title</label>
                    <input type="text" name="pages[${index}][title]" class="form-control">
                </div>
                <div class="form-group">
                    <label>Page Content</label>
                    <textarea name="pages[${index}][content]" class="form-control" rows="5"></textarea>
                </div>
            </div>
        `;
        container.appendChild(newItem);
    });

    document.getElementById('pages-container').addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-page')) {
            e.target.closest('.page-item').remove();
        }
    });
</script>

<?php include '../includes/footer.php'; ?>
