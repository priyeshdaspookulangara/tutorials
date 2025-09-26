<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php");
    exit();
}

$errors = [];
$stmt_topics = $conn->prepare("SELECT t.id, tt.name FROM topics t JOIN topic_translations tt ON t.id = tt.topic_id WHERE tt.language = ? ORDER BY tt.name ASC");
$stmt_topics->bind_param("s", $default_lang);
$stmt_topics->execute();
$topics_result = $stmt_topics->get_result();
$stmt_topics->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topic_id = (int)$_POST['topic_id'];
    $is_premium = isset($_POST['is_premium']) ? 1 : 0;
    $translations = $_POST['translations'];
    $pages = $_POST['pages'];

    if ($topic_id <= 0) {
        $errors[] = trans('err_topic_required');
    }
    if (empty($translations[$default_lang]['title'])) {
        $errors[] = trans('err_tutorial_title_required') . " (for default language {$default_lang})";
    }

    if (empty($errors)) {
        $conn->begin_transaction();
        try {
            // 1. Create parent tutorial
            $stmt_tut = $conn->prepare("INSERT INTO tutorials (topic_id, is_premium) VALUES (?, ?)");
            $stmt_tut->bind_param("ii", $topic_id, $is_premium);
            $stmt_tut->execute();
            $tutorial_id = $stmt_tut->insert_id;
            $stmt_tut->close();

            // 2. Insert tutorial translations
            $stmt_tut_trans = $conn->prepare("INSERT INTO tutorial_translations (tutorial_id, language, title) VALUES (?, ?, ?)");
            foreach ($translations as $lang_code => $data) {
                if (!empty($data['title'])) {
                    $stmt_tut_trans->bind_param("iss", $tutorial_id, $lang_code, sanitize_input($data['title']));
                    $stmt_tut_trans->execute();
                }
            }
            $stmt_tut_trans->close();

            // 3. Insert pages and their translations
            $stmt_page = $conn->prepare("INSERT INTO tutorial_pages (tutorial_id, page_number) VALUES (?, ?)");
            $stmt_page_trans = $conn->prepare("INSERT INTO tutorial_page_translations (page_id, language, title, content) VALUES (?, ?, ?, ?)");
            foreach ($pages as $index => $page_data) {
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
            $errors[] = trans('err_tutorial_add_failed') . " " . $e->getMessage();
        }
    }
}
?>

<div class="row">
    <div class="col-md-12">
        <h2><?php echo trans('add_tutorial'); ?></h2>
        <?php if (!empty($errors)) : ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error) : ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form action="add_tutorial.php" method="post">
            <div class="form-group">
                <label for="topic_id"><?php echo trans('topic'); ?></label>
                <select name="topic_id" id="topic_id" class="form-control">
                    <option value=""><?php echo trans('select_a_topic'); ?></option>
                    <?php while ($row = $topics_result->fetch_assoc()) : ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" name="is_premium" id="is_premium" class="form-check-input" value="1">
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
                <?php foreach ($available_langs as $lang_code) : ?>
                    <div class="tab-pane fade <?php echo $lang_code == $default_lang ? 'show active' : ''; ?>" id="title-<?php echo $lang_code; ?>">
                        <div class="card card-body">
                            <input type="text" name="translations[<?php echo $lang_code; ?>][title]" class="form-control" placeholder="Title in <?php echo strtoupper($lang_code); ?>">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <hr>

            <h4><?php echo trans('tutorial_pages_heading'); ?></h4>
            <div id="pages-container">
                <!-- Page 1 -->
                <div class="page-item card mb-3">
                    <div class="card-header">Page 1</div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" role="tablist">
                            <?php foreach ($available_langs as $lang_code) : ?>
                                <li class="nav-item"><a class="nav-link <?php echo $lang_code == $default_lang ? 'active' : ''; ?>" data-toggle="tab" href="#page-0-<?php echo $lang_code; ?>"><?php echo strtoupper($lang_code); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="tab-content">
                            <?php foreach ($available_langs as $lang_code) : ?>
                                <div class="tab-pane fade <?php echo $lang_code == $default_lang ? 'show active' : ''; ?>" id="page-0-<?php echo $lang_code; ?>">
                                    <div class="card card-body">
                                        <div class="form-group">
                                            <label><?php echo trans('page_title'); ?></label>
                                            <input type="text" name="pages[0][translations][<?php echo $lang_code; ?>][title]" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label><?php echo trans('page_content'); ?></label>
                                            <textarea name="pages[0][translations][<?php echo $lang_code; ?>][content]" class="form-control" rows="5"></textarea>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" id="add-page" class="btn btn-secondary"><?php echo trans('add_page'); ?></button>
            <hr>

            <button type="submit" class="btn btn-primary"><?php echo trans('add_tutorial'); ?></button>
        </form>
    </div>
</div>

<script>
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
