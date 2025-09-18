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
    $translations = $_POST['translations'];

    // Basic validation: Check if the default language has a name
    if (empty($translations[$default_lang]['name'])) {
        $errors[] = trans('err_topic_name_required') . " (for default language {$default_lang})";
    }

    if (empty($errors)) {
        // Start transaction
        $conn->begin_transaction();

        try {
            // 1. Create a new entry in the parent `topics` table
            $stmt_topic = $conn->prepare("INSERT INTO topics () VALUES ()");
            $stmt_topic->execute();
            $topic_id = $stmt_topic->insert_id;
            $stmt_topic->close();

            // 2. Insert translations for each language
            $stmt_trans = $conn->prepare("INSERT INTO topic_translations (topic_id, language, name, description) VALUES (?, ?, ?, ?)");

            foreach ($translations as $lang_code => $data) {
                $name = sanitize_input($data['name']);
                $description = sanitize_input($data['description']);
                $stmt_trans->bind_param("isss", $topic_id, $lang_code, $name, $description);
                $stmt_trans->execute();
            }
            $stmt_trans->close();

            // If everything is fine, commit the transaction
            $conn->commit();
            header("Location: view_topics.php");
            exit();

        } catch (Exception $e) {
            // If something goes wrong, roll back the transaction
            $conn->rollback();
            $errors[] = trans('err_topic_add_failed') . " " . $e->getMessage();
        }
    }
}
?>

<div class="row">
    <div class="col-md-12">
        <h2><?php echo trans('add_topic'); ?></h2>
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
            <ul class="nav nav-tabs" id="langTabs" role="tablist">
                <?php foreach ($available_langs as $lang_code): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $lang_code == $default_lang ? 'active' : ''; ?>" id="<?php echo $lang_code; ?>-tab" data-toggle="tab" href="#<?php echo $lang_code; ?>" role="tab"><?php echo strtoupper($lang_code); ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="tab-content" id="langTabsContent">
                <?php foreach ($available_langs as $lang_code): ?>
                    <div class="tab-pane fade <?php echo $lang_code == $default_lang ? 'show active' : ''; ?>" id="<?php echo $lang_code; ?>" role="tabpanel">
                        <div class="card card-body">
                            <div class="form-group">
                                <label for="name_<?php echo $lang_code; ?>"><?php echo trans('topic_name'); ?></label>
                                <input type="text" name="translations[<?php echo $lang_code; ?>][name]" id="name_<?php echo $lang_code; ?>" class="form-control" value="">
                            </div>
                            <div class="form-group">
                                <label for="description_<?php echo $lang_code; ?>"><?php echo trans('description'); ?></label>
                                <textarea name="translations[<?php echo $lang_code; ?>][description]" id="description_<?php echo $lang_code; ?>" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="btn btn-primary mt-3"><?php echo trans('add_topic'); ?></button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
