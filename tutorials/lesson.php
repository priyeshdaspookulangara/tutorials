<?php
require_once '../includes/db.php';
include '../includes/header.php';

$tutorial_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$page_number = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($tutorial_id <= 0) {
    header("Location: /");
    exit();
}

$stmt_tutorial = $conn->prepare("SELECT title, is_premium FROM tutorials WHERE id = ?");
$stmt_tutorial->bind_param("i", $tutorial_id);
$stmt_tutorial->execute();
$result_tutorial = $stmt_tutorial->get_result();
$tutorial = $result_tutorial->fetch_assoc();
$stmt_tutorial->close();

if (!$tutorial) {
    header("Location: /");
    exit();
}

if ($tutorial['is_premium'] && !isset($_SESSION['user_id'])) {
    $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
    header("Location: ../user/login.php");
    exit();
}

$stmt_page = $conn->prepare("SELECT title, content FROM tutorial_pages WHERE tutorial_id = ? AND page_number = ?");
$stmt_page->bind_param("ii", $tutorial_id, $page_number);
$stmt_page->execute();
$result_page = $stmt_page->get_result();
$page = $result_page->fetch_assoc();
$stmt_page->close();

if (!$page) {
    // If the page doesn't exist, maybe it's an invalid page number.
    // For simplicity, we'll just show a "not found" message.
    // A more robust solution would be to redirect to the first page.
    $page = ['title' => 'Page Not Found', 'content' => 'This page does not exist.'];
}

$stmt_total_pages = $conn->prepare("SELECT COUNT(*) as total FROM tutorial_pages WHERE tutorial_id = ?");
$stmt_total_pages->bind_param("i", $tutorial_id);
$stmt_total_pages->execute();
$total_pages = $stmt_total_pages->get_result()->fetch_assoc()['total'];
$stmt_total_pages->close();
?>

<div class="row">
    <div class="col-md-12">
        <h2><?php echo htmlspecialchars($tutorial['title']); ?></h2>
        <hr>
        <h3><?php echo htmlspecialchars($page['title']); ?></h3>
        <div class="lesson-content">
            <?php echo nl2br(htmlspecialchars($page['content'])); ?>
        </div>
        <hr>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-between">
                <li class="page-item <?php if ($page_number <= 1) echo 'disabled'; ?>">
                    <a class="page-link" href="?id=<?php echo $tutorial_id; ?>&page=<?php echo $page_number - 1; ?>">Previous</a>
                </li>
                <li class="page-item">
                    <span class="page-link">Page <?php echo $page_number; ?> of <?php echo $total_pages; ?></span>
                </li>
                <li class="page-item <?php if ($page_number >= $total_pages) echo 'disabled'; ?>">
                    <a class="page-link" href="?id=<?php echo $tutorial_id; ?>&page=<?php echo $page_number + 1; ?>">Next</a>
                </li>
            </ul>
        </nav>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
