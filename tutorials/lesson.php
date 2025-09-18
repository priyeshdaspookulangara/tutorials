<?php
require_once '../includes/db.php';
include '../includes/header.php';

$tutorial_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$page_number = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($tutorial_id <= 0) {
    header("Location: /");
    exit();
}

$stmt_tutorial = $conn->prepare("
    SELECT t.is_premium, t.topic_id, tt.title
    FROM tutorials t
    JOIN tutorial_translations tt ON t.id = tt.tutorial_id
    WHERE t.id = ? AND tt.language = ?
");
$stmt_tutorial->bind_param("is", $tutorial_id, $lang_to_use);
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

// Fetch all pages for the current tutorial for the new sidebar
$stmt_all_pages = $conn->prepare("
    SELECT p.page_number, pt.title
    FROM tutorial_pages p
    JOIN tutorial_page_translations pt ON p.id = pt.page_id
    WHERE p.tutorial_id = ? AND pt.language = ?
    ORDER BY p.page_number ASC
");
$stmt_all_pages->bind_param("is", $tutorial_id, $lang_to_use);
$stmt_all_pages->execute();
$all_pages_result = $stmt_all_pages->get_result();
$stmt_all_pages->close();

// Fetch the current page content
$stmt_page = $conn->prepare("
    SELECT pt.title, pt.content
    FROM tutorial_pages p
    JOIN tutorial_page_translations pt ON p.id = pt.page_id
    WHERE p.tutorial_id = ? AND p.page_number = ? AND pt.language = ?
");
$stmt_page->bind_param("iis", $tutorial_id, $page_number, $lang_to_use);
$stmt_page->execute();
$result_page = $stmt_page->get_result();
$page = $result_page->fetch_assoc();
$stmt_page->close();

if (!$page) {
    $page = ['title' => trans('page_not_found_title'), 'content' => trans('page_not_found_message')];
}

$total_pages = $all_pages_result->num_rows;
?>

<div class="row">
    <div class="col-md-3">
        <h4><?php echo trans('tutorial_pages'); ?></h4>
        <div class="list-group">
            <?php
            if ($all_pages_result->num_rows > 0) {
                // Reset pointer to loop again after calculating total_pages
                mysqli_data_seek($all_pages_result, 0);
                while ($page_row = $all_pages_result->fetch_assoc()) {
                    $active_class = ($page_row['page_number'] == $page_number) ? ' active' : '';
                    echo '<a href="?id=' . $tutorial_id . '&page=' . $page_row['page_number'] . '" class="list-group-item list-group-item-action' . $active_class . '">' . htmlspecialchars($page_row['title']) . '</a>';
                }
            }
            ?>
        </div>
    </div>

    <div class="col-md-9">
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
                    <a class="page-link" href="?id=<?php echo $tutorial_id; ?>&page=<?php echo $page_number - 1; ?>"><?php echo trans('previous_page'); ?></a>
                </li>
                <li class="page-item">
                    <span class="page-link"><?php echo sprintf(trans('page_x_of_y'), $page_number, $total_pages); ?></span>
                </li>
                <li class="page-item <?php if ($page_number >= $total_pages) echo 'disabled'; ?>">
                    <a class="page-link" href="?id=<?php echo $tutorial_id; ?>&page=<?php echo $page_number + 1; ?>"><?php echo trans('next_page'); ?></a>
                </li>
            </ul>
        </nav>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
