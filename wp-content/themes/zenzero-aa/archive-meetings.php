<?php

ob_start();
include __DIR__ . '/../../plugins/12-step-meeting-list/templates/archive-meetings.php';
$parent_content = ob_get_clean();

$parent_content = zenzero_aa_insert_sidebar($parent_content);
$parent_content = zenzero_aa_remove_unused_general_elements($parent_content);

echo $parent_content;
