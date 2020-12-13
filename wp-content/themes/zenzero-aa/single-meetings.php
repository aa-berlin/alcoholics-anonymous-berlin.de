<?php

ob_start();
include __DIR__ . '/../../plugins/12-step-meeting-list/templates/single-meetings.php';
$parent_content = ob_get_clean();

$parent_content = zenzero_aa_remove_author($parent_content);
$parent_content = zenzero_aa_add_timezone($parent_content);
$parent_content = zenzero_aa_tidy_up_html($parent_content);

echo $parent_content;
