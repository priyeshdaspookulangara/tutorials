<?php
// This component assumes $conn is available from the main script.
$topics_result = $conn->query("SELECT id, name FROM topics ORDER BY name ASC");
?>
<div class="col-md-3">
    <h4>Topics</h4>
    <div class="list-group">
        <a href="/" class="list-group-item list-group-item-action <?php echo !isset($_GET['id']) ? 'active' : ''; ?>">All Topics</a>
        <?php
        if ($topics_result && $topics_result->num_rows > 0) {
            while ($topic_row = $topics_result->fetch_assoc()) {
                // Highlight the current topic
                $active_class = '';
                if (isset($_GET['id']) && $_GET['id'] == $topic_row['id']) {
                    $active_class = ' active';
                }
                echo '<a href="/tutorials/topic.php?id=' . $topic_row['id'] . '" class="list-group-item list-group-item-action' . $active_class . '">' . htmlspecialchars($topic_row['name']) . '</a>';
            }
        }
        ?>
    </div>
</div>
