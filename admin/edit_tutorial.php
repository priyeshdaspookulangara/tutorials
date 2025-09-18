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

// Fetch basic tutorial data
$stmt_tut = $conn->prepare("SELECT topic_id, is_premium FROM tutorials WHERE id = ?");
$stmt_tut->bind_param("i", $tutorial_id);
$stmt_tut->execute();
$tutorial = $stmt_tut->get_result()->fetch_assoc();
$stmt_tut->close();

if (!$tutorial) {
    header("Location: view_tutorials.php");
    exit();
}

// Fetch title translations
$stmt_title_trans = $conn->prepare("SELECT language, title FROM tutorial_translations WHERE tutorial_id = ?");
$stmt_title_trans->bind_param("i", $tutorial_id);
$stmt_title_trans->execute();
$title_translations = [];
$result_title_trans = $stmt_title_trans->get_result();
while($row = $result_title_trans->fetch_assoc()) {
    $title_translations[$row['language']] = $row;
}
$stmt_title_trans->close();

// Fetch pages and their translations
$stmt_pages = $conn->prepare("
    SELECT p.id as page_id, p.page_number, pt.language, pt.title, pt.content
    FROM tutorial_pages p
    LEFT JOIN tutorial_page_translations pt ON p.id = pt.page_id
    WHERE p.tutorial_id = ?
    ORDER BY p.page_number, pt.language
");
$stmt_pages->bind_param("i", $tutorial_id);
$stmt_pages->execute();
$pages_result = $stmt_pages->get_result();
$pages = [];
while($row = $pages_result->fetch_assoc()) {
    $pages[$row['page_id']]['page_number'] = $row['page_number'];
    $pages[$row['page_id']]['translations'][$row['language']] = [
        'title' => $row['title'],
        'content' => $row['content']
    ];
}
$stmt_pages->close();

$topics_result = $conn->query("SELECT t.id, tt.name FROM topics t JOIN topic_translations tt ON t.id = tt.topic_id WHERE tt.language = '{$default_lang}' ORDER BY tt.name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topic_id = (int)$_POST['topic_id'];
    $is_premium = isset($_POST['is_premium']) ? 1 : 0;
    $translations = $_POST['translations'];
    $posted_pages = $_POST['pages'] ?? [];

    if (empty($translations[$default_lang]['title'])) {
        $errors[] = trans('err_tutorial_title_required');
    }

    if (empty($errors)) {
        $conn->begin_transaction();
        try {
            // 1. Update parent tutorial
            $stmt_tut_update = $conn->prepare("UPDATE tutorials SET topic_id = ?, is_premium = ? WHERE id = ?");
            $stmt_tut_update->bind_param("iii", $topic_id, $is_premium, $tutorial_id);
            $stmt_tut_update->execute();
            $stmt_tut_update->close();

            // 2. Update/Insert tutorial translations
            $stmt_tut_trans = $conn->prepare("INSERT INTO tutorial_translations (tutorial_id, language, title) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE title = VALUES(title)");
            foreach ($translations as $lang_code => $data) {
                if (!empty($data['title'])) {
                    $stmt_tut_trans->bind_param("iss", $tutorial_id, $lang_code, sanitize_input($data['title']));
                    $stmt_tut_trans->execute();
                }
            }
            $stmt_tut_trans->close();

            // 3. Update/Insert/Delete pages and translations
            // For simplicity, we delete all pages and re-insert them. A more complex UI would be needed for a better UX.
            $stmt_del_pages = $conn->prepare("DELETE FROM tutorial_pages WHERE tutorial_id = ?");
            $stmt_del_pages->bind_param("i", $tutorial_id);
            $stmt_del_pages->execute();
            $stmt_del_pages->close();

            $stmt_page = $conn->prepare("INSERT INTO tutorial_pages (tutorial_id, page_number) VALUES (?, ?)");
            $stmt_page_trans = $conn->prepare("INSERT INTO tutorial_page_translations (page_id, language, title, content) VALUES (?, ?, ?, ?)");
            foreach ($posted_pages as $index => $page_data) {
                $page_number = $index + 1;
                $stmt_page->bind_param("ii", $tutorial_id, $page_number);
                $stmt_page->execute();
                $page_id = $stmt_page->insert_id;

                foreach ($page_data['translations'] as $lang_code => $trans_data) {
                    if (!empty($trans_data['title'])) {
                        $stmt_page_trans->bind_param("isss", $page_id, $lang_code, sanitize_input($trans_data['title']), sanitize_input($trans_data['content']));
                        $stmt_page_trans->execute();
                    }
                }
            }
            $stmt_page->close();
            $stmt_page_trans->close();

            $conn->commit();
            header("Location: view_tutorials.php");
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            $errors[] = trans('err_tutorial_update_failed') . " " . $e->getMessage();
        }
    }
}
?>

<div class="row">
    <div class="col-md-12">
        <h2><?php echo trans('edit_tutorial'); ?></h2>
        <?php if (!empty($errors)) : ?>
            <div class="alert alert-danger"><ul><?php foreach ($errors as $error) : ?><li><?php echo $error; ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>
        <form action="edit_tutorial.php?id=<?php echo $tutorial_id; ?>" method="post">
            <div class="form-group">
                <label for="topic_id"><?php echo trans('topic'); ?></label>
                <select name="topic_id" id="topic_id" class="form-control">
                    <?php while ($row = $topics_result->fetch_assoc()) : ?>
                        <option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $tutorial['topic_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($row['name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" name="is_premium" id="is_premium" class="form-check-input" value="1" <?php echo $tutorial['is_premium'] ? 'checked' : ''; ?>>
                <label for="is_premium" class="form-check-label"><?php echo trans('is_premium'); ?></label>
            </div>

            <hr>
            <h4><?php echo trans('tutorial_title'); ?></h4>
            <ul class="nav nav-tabs" role="tablist">
                <?php foreach ($available_langs as $lang_code) : ?>
                    <li class="nav-item"><a class="nav-link <?php echo $lang_code == $default_lang ? 'active' : ''; ?>" data-toggle="tab" href="#title-<?php echo $lang_code; ?>"><?php echo strtoupper($lang_code); ?></a></li>
                <?php endforeach; ?>
            </ul>
            <div class="tab-content">
                <?php foreach ($available_langs as $lang_code) :
                    $title_trans = $title_translations[$lang_code] ?? ['title' => ''];
                ?>
                    <div class="tab-pane fade <?php echo $lang_code == $default_lang ? 'show active' : ''; ?>" id="title-<?php echo $lang_code; ?>">
                        <div class="card card-body">
                            <input type="text" name="translations[<?php echo $lang_code; ?>][title]" class="form-control" value="<?php echo htmlspecialchars($title_trans['title']); ?>">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <hr>

            <h4><?php echo trans('tutorial_pages_heading'); ?></h4>
            <div id="pages-container">
                <?php if (!empty($pages)): $page_idx = 0; foreach ($pages as $page_id => $page_data): ?>
                <div class="page-item card mb-3">
                    <div class="card-header d-flex justify-content-between">
                        <span>Page <?php echo $page_idx + 1; ?></span>
                        <button type="button" class="btn btn-sm btn-danger remove-page"><?php echo trans('remove_page'); ?></button>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" role="tablist">
                            <?php foreach ($available_langs as $lang_code) : ?>
                                <li class="nav-item"><a class="nav-link <?php echo $lang_code == $default_lang ? 'active' : ''; ?>" data-toggle="tab" href="#page-<?php echo $page_idx; ?>-<?php echo $lang_code; ?>"><?php echo strtoupper($lang_code); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="tab-content">
                            <?php foreach ($available_langs as $lang_code) :
                                $page_trans = $page_data['translations'][$lang_code] ?? ['title' => '', 'content' => ''];
                            ?>
                                <div class="tab-pane fade <?php echo $lang_code == $default_lang ? 'show active' : ''; ?>" id="page-<?php echo $page_idx; ?>-<?php echo $lang_code; ?>">
                                    <div class="card card-body">
                                        <div class="form-group">
                                            <label><?php echo trans('page_title'); ?></label>
                                            <input type="text" name="pages[<?php echo $page_idx; ?>][translations][<?php echo $lang_code; ?>][title]" class="form-control" value="<?php echo htmlspecialchars($page_trans['title']); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label><?php echo trans('page_content'); ?></label>
                                            <textarea name="pages[<?php echo $page_idx; ?>][translations][<?php echo $lang_code; ?>][content]" class="form-control" rows="5"><?php echo htmlspecialchars($page_trans['content']); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php $page_idx++; endforeach; endif; ?>
            </div>
            <button type="button" id="add-page" class="btn btn-secondary"><?php echo trans('add_page'); ?></button>
            <hr>

            <button type="submit" class="btn btn-primary"><?php echo trans('edit_tutorial'); ?></button>
        </form>
    </div>
</div>

<script>
// Same JS as add_tutorial.php
document.getElementById('add-page').addEventListener('click', function() {
    const container = document.getElementById('pages-container');
    const index = container.children.length;
    const page_item = document.createElement('div');
    page_item.className = 'page-item card mb-3';

    let lang_tabs = '';
    let tab_content = '';
    <?php foreach ($available_langs as $lang_code) : ?>
        lang_tabs += `<li class="nav-item"><a class="nav-link <?php echo $lang_code == $default_lang ? 'active' : ''; ?>" data-toggle="tab" href="#page-${index}-<?php echo $lang_code; ?>"><?php echo strtoupper($lang_code); ?></a></li>`;
        tab_content += `
            <div class="tab-pane fade <?php echo $lang_code == $default_lang ? 'show active' : ''; ?>" id="page-${index}-<?php echo $lang_code; ?>">
                <div class="card card-body">
                    <div class="form-group">
                        <label><?php echo trans('page_title'); ?></label>
                        <input type="text" name="pages[${index}][translations][<?php echo $lang_code; ?>][title]" class="form-control">
                    </div>
                    <div class="form-group">
                        <label><?php echo trans('page_content'); ?></label>
                        <textarea name="pages[${index}][translations][<?php echo $lang_code; ?>][content]" class="form-control" rows="5"></textarea>
                    </div>
                </div>
            </div>
        `;
    <?php endforeach; ?>

    page_item.innerHTML = `
        <div class="card-header d-flex justify-content-between">
            <span>Page ${index + 1}</span>
            <button type="button" class="btn btn-sm btn-danger remove-page"><?php echo trans('remove_page'); ?></button>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">${lang_tabs}</ul>
            <div class="tab-content">${tab_content}</div>
        </div>
    `;
    container.appendChild(page_item);
});

document.getElementById('pages-container').addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('remove-page')) {
        e.target.closest('.page-item').remove();
    }
});
</script>

<?php include '../includes/footer.php'; ?>
