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

// Fetch existing translations
$stmt_fetch = $conn->prepare("SELECT language, name, description FROM topic_translations WHERE topic_id = ?");
$stmt_fetch->bind_param("i", $topic_id);
$stmt_fetch->execute();
$result = $stmt_fetch->get_result();
$existing_translations = [];
while ($row = $result->fetch_assoc()) {
    $existing_translations[$row['language']] = $row;
}
$stmt_fetch->close();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $translations = $_POST['translations'];

    if (empty($translations[$default_lang]['name'])) {
        $errors[] = trans('err_topic_name_required') . " (for default language {$default_lang})";
    }

    if (empty($errors)) {
        $conn->begin_transaction();
        try {
            // Use INSERT ... ON DUPLICATE KEY UPDATE to handle both new and existing languages
            $stmt_trans = $conn->prepare("
                INSERT INTO topic_translations (topic_id, language, name, description)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description)
            ");

            foreach ($translations as $lang_code => $data) {
                if (!empty($data['name'])) { // Only insert/update if a name is provided
                    $name = sanitize_input($data['name']);
                    $description = sanitize_input($data['description']);
                    $stmt_trans->bind_param("isss", $topic_id, $lang_code, $name, $description);
                    $stmt_trans->execute();
                }
            }
            $stmt_trans->close();

            $conn->commit();
            header("Location: view_topics.php");
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            $errors[] = trans('err_topic_update_failed') . " " . $e->getMessage();
        }
    }
}
?>

<div class="row">
    <div class="col-md-12">
        <h2><?php echo trans('edit_topic'); ?></h2>
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
            <ul class="nav nav-tabs" id="langTabs" role="tablist">
                <?php foreach ($available_langs as $lang_code): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $lang_code == $default_lang ? 'active' : ''; ?>" id="<?php echo $lang_code; ?>-tab" data-toggle="tab" href="#<?php echo $lang_code; ?>" role="tab"><?php echo strtoupper($lang_code); ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="tab-content" id="langTabsContent">
                <?php foreach ($available_langs as $lang_code):
                    $translation = $existing_translations[$lang_code] ?? ['name' => '', 'description' => ''];
                ?>
                    <div class="tab-pane fade <?php echo $lang_code == $default_lang ? 'show active' : ''; ?>" id="<?php echo $lang_code; ?>" role="tabpanel">
                        <div class="card card-body">
                            <div class="form-group">
                                <label for="name_<?php echo $lang_code; ?>"><?php echo trans('topic_name'); ?></label>
                                <input type="text" name="translations[<?php echo $lang_code; ?>][name]" id="name_<?php echo $lang_code; ?>" class="form-control" value="<?php echo htmlspecialchars($translation['name']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="description_<?php echo $lang_code; ?>"><?php echo trans('description'); ?></label>
                                <textarea name="translations[<?php echo $lang_code; ?>][description]" id="description_<?php echo $lang_code; ?>" class="form-control"><?php echo htmlspecialchars($translation['description']); ?></textarea>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="btn btn-primary mt-3"><?php echo trans('edit_topic'); ?></button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
