<?php

ob_start();
include __DIR__ . '/../zenzero/sidebar.php';
$parent_content = ob_get_clean();

$parent_content = str_replace(['smallPart', 'nano-content', 'nano'], ['', 'sidebar-content', 'disabled-nano'], $parent_content);

echo ZENZERO_AA_SIDEBAR_DELIMITER;
echo $parent_content;
echo ZENZERO_AA_SIDEBAR_DELIMITER;
